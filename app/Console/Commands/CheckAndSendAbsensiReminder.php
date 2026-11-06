<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppReminderJob;
use App\Models\Absensi;
use App\Models\AttendanceSetting;
use App\Models\ReminderLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckAndSendAbsensiReminder extends Command
{
    protected $signature   = 'absensi:send-reminder {--force : Bypass pengecekan waktu untuk testing}';
    protected $description = 'Cek waktu & dispatch job reminder WA absensi (dijalankan tiap 5 menit)';

    protected int $windowMenit = 3;

    public function handle(): int
    {
        $sekarang     = Carbon::now();
        $hariIni      = strtolower($sekarang->englishDayOfWeek);
        $menitSebelum = (int) AttendanceSetting::get('reminder_before_minutes', 60);

        $this->info("[{$sekarang->format('H:i:s')}] Hari: {$hariIni}");

        $checkInTime  = AttendanceSetting::get("check_in_{$hariIni}", '08:00');
        $checkOutTime = AttendanceSetting::get("check_out_{$hariIni}", '16:00');

        $targetMasuk  = Carbon::parse($checkInTime)->subMinutes($menitSebelum);
        $targetPulang = Carbon::parse($checkOutTime)->subMinutes($menitSebelum);

        $this->line("  Jam masuk  : {$checkInTime} -> reminder jam {$targetMasuk->format('H:i')}");
        $this->line("  Jam pulang : {$checkOutTime} -> reminder jam {$targetPulang->format('H:i')}");
        $this->line("  Sekarang   : {$sekarang->format('H:i')}");

        if ($this->option('force')) {
            $this->warn('Mode FORCE aktif — bypass pengecekan waktu!');
            $this->dispatchReminders('masuk', $checkInTime);
            return self::SUCCESS;
        }

        $dispatch = false;

        if ($this->dalamWindow($sekarang, $targetMasuk)) {
            $this->info("Dalam window reminder MASUK");
            $this->dispatchReminders('masuk', $checkInTime);
            $dispatch = true;
        }

        if ($this->dalamWindow($sekarang, $targetPulang)) {
            $this->info("Dalam window reminder PULANG");
            $this->dispatchReminders('pulang', $checkOutTime);
            $dispatch = true;
        }

        if (! $dispatch) {
            $this->line("  Bukan waktu reminder, tidak ada yang dikirim.");
        }

        return self::SUCCESS;
    }

    protected function dispatchReminders(string $type, string $jamKerja): void
    {
        $pegawai = User::where('is_active', true)
            ->where('role', 'user')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        if ($pegawai->isEmpty()) {
            $this->warn('  Tidak ada pegawai aktif dengan nomor WA.');
            return;
        }

        $today   = Carbon::today();
        $userIds = $pegawai->pluck('id');

        $sudahAbsen = $type === 'masuk'
            ? Absensi::whereIn('user_id', $userIds)->whereDate('tanggal', $today)->whereNotNull('check_in')->pluck('user_id')
            : Absensi::whereIn('user_id', $userIds)->whereDate('tanggal', $today)->whereNotNull('check_out')->pluck('user_id');

        $sudahDikirim = ReminderLog::whereIn('user_id', $userIds)
            ->where('type', $type)
            ->where('status', 'sent')
            ->whereDate('sent_at', $today)
            ->pluck('user_id');

        $target = $pegawai->whereNotIn('id', $sudahAbsen)->whereNotIn('id', $sudahDikirim)->values();

        if ($target->isEmpty()) {
            $this->info("  Semua pegawai sudah absen atau sudah dapat reminder {$type}.");
            return;
        }

        $this->info("  Mendispatch {$target->count()} reminder {$type}...");

        $message    = $this->buildMessage($type, $jamKerja);
        $delayDetik = 0;

        foreach ($target as $index => $user) {
            $jeda        = rand(7, 10);
            $delayDetik += $jeda;

            SendWhatsAppReminderJob::dispatch($user, $message, $type)
                ->delay(now()->addSeconds($delayDetik))
                ->onQueue('whatsapp');

            $this->line("    [{$index}] {$user->name} ({$user->phone}) -> delay {$delayDetik}s");
        }

        $estimasi = round($delayDetik / 60, 1);
        $this->info("  Selesai dispatch. Estimasi selesai kirim: ~{$estimasi} menit.");
    }

    protected function dalamWindow(Carbon $sekarang, Carbon $target): bool
    {
        $diff = abs($sekarang->diffInMinutes($target, false));
        return $diff <= $this->windowMenit;
    }

    protected function buildMessage(string $type, string $jamKerja): string
    {
        $jam   = Carbon::parse($jamKerja)->format('H:i');
        $hari  = Carbon::now()->translatedFormat('l, d F Y');
        $emoji = $type === 'masuk' ? '🌅' : '🌆';
        $jenis = $type === 'masuk' ? 'Check-In (Masuk Kerja)' : 'Check-Out (Pulang Kerja)';

        return
            "{$emoji} *PENGINGAT ABSENSI*\n\n" .
            "Kepada Yth. Bapak/Ibu Pegawai,\n" .
            "PTPN IV Regional 7\n\n" .
            "Ini adalah pengingat otomatis:\n\n" .
            "Jenis   : {$jenis}\n" .
            "Jam     : {$jam} WIB\n" .
            "Tanggal : {$hari}\n\n" .
            "Mohon segera lakukan absensi melalui sistem.\n\n" .
            "_Sistem Informasi Absensi_\n" .
            "_PTPN IV Regional 7_";
    }
}