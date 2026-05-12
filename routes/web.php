<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Logout;
use App\Livewire\Pages\AdminProfile;
use App\Livewire\Pages\AdminUsers;
use App\Livewire\Pages\Bimbingan;
use App\Livewire\Pages\Dashboard;
use App\Livewire\Pages\Dosen;
use App\Livewire\Pages\DosenBimbinganLog;
use App\Livewire\Pages\DosenBimbinganOnline;
use App\Livewire\Pages\DosenKontrolBimbingan;
use App\Livewire\Pages\DosenDashboard;
use App\Livewire\Pages\DosenProfile;
use App\Livewire\Pages\JadwalSidang;
use App\Livewire\Pages\KaprodiDashboard;
use App\Livewire\Pages\KaprodiLaporan;
use App\Livewire\Pages\KaprodiManagement;
use App\Livewire\Pages\KaprodiSidangApproval;
use App\Livewire\Pages\Laporan;
use App\Livewire\Pages\Mahasiswa;
use App\Livewire\Pages\MahasiswaBimbingan;
use App\Livewire\Pages\MahasiswaBimbinganOnline;
use App\Livewire\Pages\MahasiswaDashboard;
use App\Livewire\Pages\MahasiswaDokumen;
use App\Livewire\Pages\MahasiswaPengajuanJudul;
use App\Livewire\Pages\MahasiswaPengajuanSidang;
use App\Livewire\Pages\MahasiswaProfile;
use App\Livewire\Pages\MahasiswaProgressDetail;
use App\Livewire\Pages\PengajuanJudulReview;
use App\Livewire\Pages\PengelolaanDokumen;
use App\Livewire\Pages\ProdiManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $user = $request->user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->hasRole('admin')) {
        return redirect()->route('dashboard');
    }

    if ($user->hasRole('dosen')) {
        return redirect()->route('dosen.dashboard');
    }

    if ($user->hasRole('mahasiswa')) {
        return redirect()->route('mahasiswa.dashboard');
    }

    if ($user->hasRole('kaprodi') || $user->hasRole('pimpinan')) {
        return redirect()->route('kaprodi.dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::livewire('/login', Login::class)->name('login')->middleware('guest');
Route::livewire('/logout', Logout::class)->name('logout');
Route::view('/forgot-password-info', 'auth.forgot-password-info')->name('forgot-password.info')->middleware('guest');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::livewire('/dashboard', Dashboard::class)->name('dashboard');
    Route::livewire('/profil-admin', AdminProfile::class)->name('admin.profile');
    Route::livewire('/user-admin', AdminUsers::class)->name('admin.users');
    Route::livewire('/kaprodi', KaprodiManagement::class)->name('admin.kaprodi');
    Route::livewire('/program-studi', ProdiManagement::class)->name('admin.prodi');
    Route::livewire('/mahasiswa', Mahasiswa::class)->name('mahasiswa');
    Route::livewire('/mahasiswa/{mahasiswaId}/progres', MahasiswaProgressDetail::class)->name('mahasiswa.progres');
    Route::livewire('/dosen', Dosen::class)->name('dosen');
    Route::livewire('/bimbingan', Bimbingan::class)->name('bimbingan');
    Route::livewire('/jadwal-sidang', JadwalSidang::class)->name('jadwal-sidang');
    Route::livewire('/laporan', Laporan::class)->name('laporan');
    Route::livewire('/pengelolaan-dokumen', PengelolaanDokumen::class)->name('pengelolaan-dokumen');
    Route::get('/penentuan-penguji', fn() => redirect()->route('jadwal-sidang'))->name('penentuan-penguji');
});

Route::middleware(['auth', 'role:dosen'])->group(function () {
    Route::livewire('/dosen/dashboard', DosenDashboard::class)->name('dosen.dashboard');
    Route::livewire('/dosen/profil', DosenProfile::class)->name('dosen.profile');
    Route::livewire('/dosen/pengajuan-judul', PengajuanJudulReview::class)->name('dosen.pengajuan-judul');
    Route::livewire('/dosen/bimbingan', DosenBimbinganLog::class)->name('dosen.bimbingan');
    Route::livewire('/dosen/bimbingan-online', DosenBimbinganOnline::class)->name('dosen.bimbingan-online');
    Route::livewire('/dosen/kontrol-bimbingan', DosenKontrolBimbingan::class)->name('dosen.kontrol-bimbingan');
});

Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::livewire('/mahasiswa/dashboard', MahasiswaDashboard::class)->name('mahasiswa.dashboard');

    Route::middleware('mahasiswa.has.pembimbing')->group(function () {
        Route::livewire('/mahasiswa/profil', MahasiswaProfile::class)->name('mahasiswa.profile');
        Route::livewire('/mahasiswa/bimbingan', MahasiswaBimbingan::class)->name('mahasiswa.bimbingan');
        Route::livewire('/mahasiswa/bimbingan-online', MahasiswaBimbinganOnline::class)->name('mahasiswa.bimbingan-online');
        Route::livewire('/mahasiswa/dokumen', MahasiswaDokumen::class)->name('mahasiswa.dokumen');
        Route::livewire('/mahasiswa/pengajuan-judul', MahasiswaPengajuanJudul::class)->name('mahasiswa.pengajuan-judul');
        Route::livewire('/mahasiswa/pengajuan-sidang', MahasiswaPengajuanSidang::class)->name('mahasiswa.pengajuan-sidang');
    });
});

Route::middleware(['auth', 'role:kaprodi|pimpinan'])->group(function () {
    Route::livewire('/kaprodi/dashboard', KaprodiDashboard::class)->name('kaprodi.dashboard');
    Route::livewire('/kaprodi/laporan', KaprodiLaporan::class)->name('kaprodi.laporan');
    Route::livewire('/kaprodi/approval-sidang', KaprodiSidangApproval::class)->name('kaprodi.approval-sidang');
    Route::livewire('/kaprodi/profil', AdminProfile::class)->name('kaprodi.profile');
    Route::get('/kaprodi/laporan/pdf', function () {
        $summary = app(\App\Support\KaprodiReportSummary::class)->build(user: Auth::user());

        return view('reports.kaprodi-laporan-pdf', [
            'statistik' => $summary['statistik'],
            'bebanDosen' => $summary['beban_dosen'],
            'detailRows' => $summary['progress_rows']->sortByDesc('progress')->values(),
        ]);
    })->name('kaprodi.laporan.pdf');
});
