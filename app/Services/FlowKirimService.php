<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * FlowKirim WhatsApp Service
 *
 * Dokumentasi API: https://flowkirim.com/docs
 * Endpoint        : POST https://api.flowkirim.com/v1/send
 * Auth            : Bearer Token (dari dashboard FlowKirim)
 */
class FlowKirimService
{
    protected string $token;
    protected string $baseUrl;
    protected string $senderNumber;

    public function __construct()
    {
        $this->token        = config('services.flowkirim.token');
        $this->baseUrl      = config('services.flowkirim.base_url', 'https://api.flowkirim.com/v1');
        $this->senderNumber = config('services.flowkirim.sender');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // KIRIM KE SATU NOMOR
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Kirim pesan WA ke satu nomor.
     *
     * @param  string  $phone    Nomor tujuan (format bebas: 08xxx / 628xxx / +62xxx)
     * @param  string  $message  Isi pesan (mendukung *bold*, _italic_, newline \n)
     * @return array  ['success' => bool, 'message_id' => string|null, 'error' => string|null]
     */
    public function send(string $phone, string $message): array
    {
        $formatted = $this->formatPhone($phone);

        try {
            $response = Http::withToken($this->token)
                ->timeout(15)
                ->retry(2, 1000) // retry 2x jika gagal, jeda 1 detik
                ->post("{$this->baseUrl}/send", [
                    'to'      => $formatted,
                    'message' => $message,
                    'sender'  => $this->senderNumber,
                    'type'    => 'text',
                ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? '') === 'success') {
                Log::info('[FlowKirim] Pesan terkirim', [
                    'to'         => $formatted,
                    'message_id' => $body['message_id'] ?? null,
                ]);

                return [
                    'success'    => true,
                    'message_id' => $body['message_id'] ?? null,
                    'error'      => null,
                ];
            }

            // Respons HTTP OK tapi status gagal
            $errorMsg = $body['message'] ?? 'Unknown error dari FlowKirim';
            Log::warning('[FlowKirim] Gagal kirim', [
                'to'       => $formatted,
                'response' => $body,
            ]);

            return ['success' => false, 'message_id' => null, 'error' => $errorMsg];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('[FlowKirim] Connection error', ['error' => $e->getMessage(), 'to' => $formatted]);
            return ['success' => false, 'message_id' => null, 'error' => 'Connection error: ' . $e->getMessage()];

        } catch (\Exception $e) {
            Log::error('[FlowKirim] Exception', ['error' => $e->getMessage(), 'to' => $formatted]);
            return ['success' => false, 'message_id' => null, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CEK SALDO / STATUS AKUN
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Cek saldo atau status device FlowKirim.
     * Berguna untuk monitoring sebelum blast.
     */
    public function checkStatus(): array
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout(10)
                ->get("{$this->baseUrl}/account/status");

            return $response->json() ?? [];

        } catch (\Exception $e) {
            Log::error('[FlowKirim] Gagal cek status', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FORMAT NOMOR TELEPON
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Normalisasi nomor ke format internasional tanpa tanda +.
     *
     * 08123456789  → 628123456789
     * +6281234     → 6281234
     * 6281234      → 6281234 (tidak berubah)
     */
    public function formatPhone(string $phone): string
    {
        // Hapus semua karakter selain angka
        $phone = preg_replace('/\D/', '', $phone);

        // Ganti awalan 0 dengan 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VALIDASI NOMOR WA (opsional, jika FlowKirim mendukung endpoint check)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Validasi apakah nomor terdaftar di WhatsApp.
     * Beberapa provider menyediakan endpoint ini — sesuaikan jika FlowKirim mendukung.
     */
    public function isValidWhatsApp(string $phone): bool
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout(10)
                ->post("{$this->baseUrl}/check-number", [
                    'phone' => $this->formatPhone($phone),
                ]);

            return ($response->json('is_whatsapp') ?? false) === true;

        } catch (\Exception $e) {
            // Jika endpoint tidak ada, anggap valid agar tidak block pengiriman
            return true;
        }
    }
}