<?php

namespace App\Console\Commands;

use App\Models\BimbinganLog;
use App\Models\BimbinganSessionAudit;
use Illuminate\Console\Command;

class AutoCompleteBimbinganSessions extends Command
{
    protected $signature = 'bimbingan:auto-complete-sessions';

    protected $description = 'Menandai sesi bimbingan menjadi selesai secara otomatis jika jadwal sudah terlewati dan mahasiswa hadir.';

    public function handle(): int
    {
        $today = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        $count = 0;

        BimbinganLog::query()
            ->where('status_sesi', 'disetujui')
            ->where('konfirmasi_mahasiswa', 'hadir')
            ->where(function ($query) use ($today, $nowTime) {
                $query->whereDate('tanggal', '<', $today)
                    ->orWhere(function ($sub) use ($today, $nowTime) {
                        $sub->whereDate('tanggal', '=', $today)
                            ->where(function ($q) use ($nowTime) {
                                $q->whereNull('jam')
                                    ->orWhere('jam', '<=', $nowTime);
                            });
                    });
            })
            ->chunkById(100, function ($logs) use (&$count) {
                foreach ($logs as $log) {
                    if (! $log instanceof BimbinganLog) {
                        continue;
                    }

                    $fromStatus = $log->status_sesi;

                    $log->update([
                        'status_sesi' => 'selesai',
                    ]);

                    BimbinganSessionAudit::query()->create([
                        'bimbingan_log_id' => $log->id,
                        'changed_by_user_id' => null,
                        'from_status_sesi' => $fromStatus,
                        'to_status_sesi' => 'selesai',
                        'source' => 'scheduler',
                        'note' => 'Status sesi diubah otomatis oleh sistem karena jadwal telah terlewati dan mahasiswa hadir.',
                        'changed_at' => now(),
                    ]);

                    $count++;
                }
            });

        $this->info("Sesi bimbingan yang ditandai selesai: {$count}");

        return self::SUCCESS;
    }
}
