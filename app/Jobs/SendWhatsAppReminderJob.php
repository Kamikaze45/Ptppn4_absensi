<?php

namespace App\Jobs;

use App\Models\ReminderLog;
use App\Models\User;
use App\Services\FlowKirimService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job: Kirim 1 pesan WA reminder absensi ke 1 pegawai.
 *
 * Setiap pegawai mendapat Job terpisah dengan delay berbeda-beda,
 * sehingga pesan tidak dikirim sekaligus (anti-spam / anti-blokir WA).
 *
 * Cara dispatch (dari Command):
 *   SendWhatsAppReminderJob::dispatch($user, $message, $type)
 *       ->delay(now()->addSeconds($delayDetik));
 */
class SendWhatsAppReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah maksimal percobaan jika job gagal.
     * Jika gagal 3x, job masuk ke failed_jobs.
     */
    public int $tries = 3;

    /**
     * Jeda antar retry (dalam detik): 30s, 60s, 120s
     */
    public array $backoff = [30, 60, 120];

    /**
     * Timeout maksimal eksekusi job (detik).
     */
    public int $timeout = 30;

    // ─────────────────────────────────────────────────────────────────────────

    public function __construct(
        protected User   $user,
        protected string $message,
        protected string $type   // 'masuk' | 'pulang'
    ) {}

    // ─────────────────────────────────────────────────────────────────────────

    public function handle(FlowKirimService $flowKirim): void
    {
        // 1. Pastikan user masih aktif dan masih punya nomor WA
        if (! $this->user->is_active || empty($this->user->phone)) {
            Log::info("[ReminderJob] Skip user #{$this->user->id} — tidak aktif atau tanpa nomor WA");
            return;
        }

        // 2. Cek apakah reminder tipe ini sudah pernah dikirim hari ini
        //    (mencegah double-kirim jika scheduler jalan 2x)
        $sudahTerkirim = ReminderLog::where('user_id', $this->user->id)
            ->where('type', $this->type)
            ->whereDate('sent_at', today())
            ->exists();

        if ($sudahTerkirim) {
            Log::info("[ReminderJob] Skip user #{$this->user->id} — reminder {$this->type} sudah terkirim hari ini");
            return;
        }

        // 3. Kirim via FlowKirim
        $result = $flowKirim->send($this->user->phone, $this->message);

        // 4. Catat ke log apapun hasilnya
        ReminderLog::create([
            'user_id'    => $this->user->id,
            'type'       => $this->type,
            'phone'      => $this->user->phone,
            'status'     => $result['success'] ? 'sent' : 'failed',
            'message_id' => $result['message_id'] ?? null,
            'error'      => $result['error'] ?? null,
            'sent_at'    => now(),
        ]);

        if ($result['success']) {
            Log::info("[ReminderJob] Berhasil ke {$this->user->name} ({$this->user->phone})");
        } else {
            Log::warning("[ReminderJob] Gagal ke {$this->user->name}: " . $result['error']);

            // Lempar exception agar job di-retry (memanfaatkan $backoff di atas)
            throw new \RuntimeException("[FlowKirim] Gagal kirim ke {$this->user->phone}: " . $result['error']);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Dipanggil setelah semua retry habis dan job tetap gagal.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("[ReminderJob] FINAL FAIL untuk user #{$this->user->id} ({$this->user->name}): " . $exception->getMessage());

        // Update status log menjadi 'failed' permanen
        ReminderLog::where('user_id', $this->user->id)
            ->where('type', $this->type)
            ->whereDate('sent_at', today())
            ->update(['status' => 'failed', 'error' => $exception->getMessage()]);
    }
}