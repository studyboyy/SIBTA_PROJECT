<?php

namespace App\Support;

use App\Models\Bimbingans;
use App\Models\DokumenTa;
use App\Models\Pengajuanjuduls;
use App\Models\PengajuanSidang;
use App\Models\SupervisorApproval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SupervisorApprovalSync
{
    public function record(Model $approvable, int $mahasiswaId, int $dosenId, string $status, ?string $catatan = null): void
    {
        SupervisorApproval::query()->updateOrCreate(
            [
                'approvable_type' => $approvable::class,
                'approvable_id' => $approvable->getKey(),
                'dosen_id' => $dosenId,
            ],
            [
                'mahasiswa_id' => $mahasiswaId,
                'status' => $status,
                'catatan' => $catatan,
                'approved_at' => $status === 'approved' || $status === 'disetujui' ? now() : null,
            ]
        );
    }

    public function supervisorIds(int $mahasiswaId): Collection
    {
        return Bimbingans::activeSupervisorIds($mahasiswaId);
    }

    public function summary(Model $approvable, int $mahasiswaId): array
    {
        $supervisorIds = $this->supervisorIds($mahasiswaId);
        $approvals = SupervisorApproval::query()
            ->where('approvable_type', $approvable::class)
            ->where('approvable_id', $approvable->getKey())
            ->whereIn('dosen_id', $supervisorIds)
            ->get()
            ->keyBy('dosen_id');

        $approved = $supervisorIds->filter(fn (int $dosenId) => in_array((string) ($approvals[$dosenId]->status ?? 'pending'), ['approved', 'disetujui'], true));
        $rejected = $supervisorIds->filter(fn (int $dosenId) => in_array((string) ($approvals[$dosenId]->status ?? 'pending'), ['rejected', 'ditolak'], true));
        $revision = $supervisorIds->filter(fn (int $dosenId) => (string) ($approvals[$dosenId]->status ?? 'pending') === 'revisi');

        return [
            'total' => $supervisorIds->count(),
            'approved' => $approved->count(),
            'rejected' => $rejected->count(),
            'revision' => $revision->count(),
            'pending' => max($supervisorIds->count() - $approved->count() - $rejected->count() - $revision->count(), 0),
        ];
    }

    public function syncTitle(Pengajuanjuduls $pengajuan): void
    {
        $summary = $this->summary($pengajuan, (int) $pengajuan->mahasiswa_id);

        $status = match (true) {
            $summary['rejected'] > 0 => 'rejected',
            $summary['revision'] > 0 => 'revisi',
            $summary['total'] > 0 && $summary['approved'] >= $summary['total'] => 'approved',
            default => 'pending',
        };

        $pengajuan->updateQuietly(['status' => $status]);
    }

    public function syncDocument(DokumenTa $dokumen): void
    {
        $summary = $this->summary($dokumen, (int) $dokumen->mahasiswa_id);

        $status = match (true) {
            $summary['rejected'] > 0 => 'ditolak',
            $summary['revision'] > 0 => 'revisi',
            $summary['total'] > 0 && $summary['approved'] >= $summary['total'] => 'disetujui',
            default => 'pending',
        };

        $dokumen->updateQuietly(['status' => $status]);
    }

    public function syncSidang(PengajuanSidang $pengajuan): void
    {
        $summary = $this->summary($pengajuan, (int) $pengajuan->mahasiswa_id);

        $status = match (true) {
            $summary['rejected'] > 0 => 'rejected',
            $summary['revision'] > 0 => 'revisi',
            $summary['total'] > 0 && $summary['approved'] >= $summary['total'] => 'approved',
            default => 'pending',
        };

        $pengajuan->updateQuietly([
            'status_dosen' => $status,
            'acc_kelayakan_at' => $status === 'approved' ? now() : null,
        ]);
    }
}
