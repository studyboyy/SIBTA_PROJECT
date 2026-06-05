<div class="">
    @php
        $isAdmin = Auth::user()->hasRole('admin');
        $isKaprodi = Auth::user()->hasRole('kaprodi') || Auth::user()->hasRole('pimpinan');
        $isMahasiswa = Auth::user()->hasRole('mahasiswa');
        $isDosen = Auth::user()->hasRole('dosen');
        $mahasiswaHasPembimbing =
            !$isMahasiswa || Auth::user()->mahasiswa?->bimbingans()->whereNotNull('dosen_id')->exists();
        $showMahasiswaLock = $isMahasiswa && !$mahasiswaHasPembimbing;
        $mahasiswaLockInfo = 'Menu ini dikunci sampai dosen pembimbing ditetapkan oleh kaprodi/admin.';
        $dashboardRoute = $isAdmin
            ? route('dashboard')
            : ($isKaprodi
                ? route('kaprodi.dashboard')
                : ($isMahasiswa
                    ? route('mahasiswa.dashboard')
                    : ($isDosen
                        ? route('dosen.dashboard')
                        : route('home'))));
    @endphp

    <div class=" bg-gray-900 lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <!-- Sidebar component, swap this element with another sidebar if you like -->
        <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4">
            <div class="flex h-16 shrink-0 items-center justify-center mt-3">
                <img src="{{ Storage::url('Logo/unpari.jpg') }}" alt="Your Company" class="h-12 w-auto" />
            </div>
            <nav class="flex flex-1 flex-col">
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                        <ul role="list" class="-mx-2 space-y-1">
                            <li>
                                <!-- Current: "bg-gray-50 text-indigo-600", Default: "text-gray-700 hover:text-indigo-600 hover:bg-gray-50" -->
                                <a href="{{ $dashboardRoute }}" wire:navigate
                                    class="data-current:bg-blue-100  group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold data-current:text-indigo-500 text-gray-600">
                                    <div
                                        class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all data-current:ml-3">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            data-slot="icon" aria-hidden="true" class="size-6 shrink-0 ">
                                            <path
                                                d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span>Dashboard</span>
                                    </div>
                                </a>
                            </li>
                            @if ($isAdmin)
                                <li>
                                    <!-- Current: "bg-gray-50 text-indigo-600", Default: "text-gray-700 hover:text-indigo-600 hover:bg-gray-50" -->
                                    <a href="{{ route('dosen') }}" wire:navigate
                                        class="data-current:bg-blue-100  group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold data-current:text-indigo-500 text-gray-600">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all data-current:ml-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                            </svg>

                                            <span>Data Dosen</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.kaprodi') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('admin.kaprodi') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('admin.kaprodi') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632ZM18 9.75h.008v.008H18V9.75Zm0 3h.008v.008H18v-.008Zm0 3h.008v.008H18v-.008Z" />
                                            </svg>
                                            <span>Kelola Kaprodi</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.prodi') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('admin.prodi') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('admin.prodi') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.75 4.5h16.5m-16.5 5.25h16.5m-16.5 5.25h10.5m-10.5 5.25h10.5" />
                                            </svg>
                                            <span>Program Studi</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <!-- Current: "bg-gray-50 text-indigo-600", Default: "text-gray-700 hover:text-indigo-600 hover:bg-gray-50" -->
                                    <a href="{{ route('mahasiswa') }}" wire:navigate
                                        class="data-current:bg-blue-100  group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold data-current:text-indigo-500 text-gray-600">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all data-current:ml-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                            </svg>
                                            <span>Data Mahasiswa</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <!-- Current: "bg-gray-50 text-indigo-600", Default: "text-gray-700 hover:text-indigo-600 hover:bg-gray-50" -->
                                    <a href="{{ route('bimbingan') }}" wire:navigate
                                        class="data-current:bg-blue-100  group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold data-current:text-indigo-500 text-gray-600">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all data-current:ml-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                            </svg>

                                            <span>Kelola Bimbingan</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('jadwal-sidang') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('jadwal-sidang') || request()->routeIs('penentuan-penguji') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('jadwal-sidang') || request()->routeIs('penentuan-penguji') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8.25 6.75h12m-12 5.25h12m-12 5.25h12M3.75 6.75h.008v.008H3.75V6.75Zm0 5.25h.008v.008H3.75V12Zm0 5.25h.008v.008H3.75v-.008Z" />
                                            </svg>
                                            <span>Jadwal Sidang & Penguji</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('pengelolaan-dokumen') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('pengelolaan-dokumen') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('pengelolaan-dokumen') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H6.75A2.25 2.25 0 004.5 4.5v15A2.25 2.25 0 006.75 21.75h10.5a2.25 2.25 0 002.25-2.25V14.25Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8.25 12h7.5m-7.5 3h4.5" />
                                            </svg>
                                            <span>Pengelolaan Dokumen</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('laporan') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('laporan') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('laporan') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 3v1.5M3 9v1.5M3 14.25v1.5M3 19.5V21m4.5-18v1.5m0 4.5v1.5m0 4.5v1.5m0 4.5V21m4.5-18v1.5m0 4.5v1.5m0 4.5v1.5m0 4.5V21m4.5-18v1.5m0 4.5v1.5m0 4.5v1.5m0 4.5V21m4.5-18v1.5m0 4.5v1.5m0 4.5v1.5m0 4.5V21" />
                                            </svg>
                                            <span>Laporan</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('admin.users') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('admin.users') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M18 7.5v9m-9-9v9m-4.5-6.75h15M4.5 18h15M6.75 4.5h10.5A2.25 2.25 0 0 1 19.5 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 17.25V6.75A2.25 2.25 0 0 1 6.75 4.5Z" />
                                            </svg>
                                            <span>User Admin</span>
                                        </div>
                                    </a>
                                </li>
                            @endif

                            @if ($isDosen)
                                <li>
                                    <a href="{{ route('dosen.pengajuan-judul') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('dosen.pengajuan-judul') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('dosen.pengajuan-judul') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM19.5 7.125 16.875 4.5" />
                                            </svg>
                                            <span>Review Pengajuan Judul</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dosen.bimbingan') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('dosen.bimbingan') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('dosen.bimbingan') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                            </svg>
                                            <span>Penjadwalan Bimbingan</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dosen.bimbingan-online') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('dosen.bimbingan-online') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('dosen.bimbingan-online') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H15.75m-8.43 4.5a18.687 18.687 0 0 1-1.281 3.527.75.75 0 0 0 1.28.798l1.896-2.845a9 9 0 1 0-1.895-1.48Z" />
                                            </svg>
                                            <span>Chat Bimbingan</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dosen.kontrol-bimbingan') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('dosen.kontrol-bimbingan') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('dosen.kontrol-bimbingan') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h12M3.75 3h16.5A2.25 2.25 0 0 1 22.5 5.25v8.25A2.25 2.25 0 0 1 20.25 15.75H6A2.25 2.25 0 0 1 3.75 13.5V3Zm10.5 16.5-2.25 2.25m0 0L9.75 19.5m2.25 2.25V15.75" />
                                            </svg>
                                            <span>Kontrol Bimbingan</span>
                                        </div>
                                    </a>
                                </li>
                            @endif

                            @if ($isKaprodi)
                                <li>
                                    <a href="{{ route('kaprodi.approval-sidang') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('kaprodi.approval-sidang') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('kaprodi.approval-sidang') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12.75 11.25 15 15 9.75m6 2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            <span>Approval Sidang</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('kaprodi.pengajuan-judul') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('kaprodi.pengajuan-judul') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('kaprodi.pengajuan-judul') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                            </svg>
                                            <span>Pengajuan Judul & Pembimbing</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('kaprodi.laporan') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('kaprodi.laporan') || request()->routeIs('kaprodi.laporan.pdf') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('kaprodi.laporan') || request()->routeIs('kaprodi.laporan.pdf') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 17.25v-6.75m3 6.75v-10.5m3 10.5V13.5m3-10.125H6.375A2.625 2.625 0 0 0 3.75 6v12a2.625 2.625 0 0 0 2.625 2.625h11.25A2.625 2.625 0 0 0 20.25 18V6a2.625 2.625 0 0 0-2.625-2.625Z" />
                                            </svg>
                                            <span>Laporan Komprehensif</span>
                                        </div>
                                    </a>
                                </li>
                            @endif

                            @if ($isMahasiswa)
                                <li>
                                    <a href="{{ route('mahasiswa.pengajuan-judul') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('mahasiswa.pengajuan-judul') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('mahasiswa.pengajuan-judul') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM19.5 7.125 16.875 4.5" />
                                            </svg>
                                            <span>Pengajuan Judul</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('mahasiswa.bimbingan') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('mahasiswa.bimbingan') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('mahasiswa.bimbingan') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                            </svg>
                                            <span>Bimbingan Saya</span>
                                            <x-ui.lock-indicator :show="$showMahasiswaLock" :message="$mahasiswaLockInfo" />
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('mahasiswa.dokumen') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('mahasiswa.dokumen') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('mahasiswa.dokumen') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H6.75A2.25 2.25 0 004.5 4.5v15A2.25 2.25 0 006.75 21.75h10.5a2.25 2.25 0 002.25-2.25V14.25Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8.25 12h7.5m-7.5 3h4.5" />
                                            </svg>
                                            <span>Dokumen Saya</span>
                                            <x-ui.lock-indicator :show="$showMahasiswaLock" :message="$mahasiswaLockInfo" />
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('mahasiswa.bimbingan-online') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('mahasiswa.bimbingan-online') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('mahasiswa.bimbingan-online') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H15.75m-8.43 4.5a18.687 18.687 0 0 1-1.281 3.527.75.75 0 0 0 1.28.798l1.896-2.845a9 9 0 1 0-1.895-1.48Z" />
                                            </svg>
                                            <span>Bimbingan Online</span>
                                            <x-ui.lock-indicator :show="$showMahasiswaLock" :message="$mahasiswaLockInfo" />
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('mahasiswa.pengajuan-sidang') }}" wire:navigate
                                        class="group hover:bg-blue-100 flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-600 {{ request()->routeIs('mahasiswa.pengajuan-sidang') ? 'bg-blue-100 text-indigo-500' : '' }}">
                                        <div
                                            class="flex items-center gap-x-3.5 group-hover:ml-3 transition-all {{ request()->routeIs('mahasiswa.pengajuan-sidang') ? 'ml-3' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 8.25v7.5a2.25 2.25 0 0 1-2.25 2.25h-10.5a2.25 2.25 0 0 1-2.25-2.25v-7.5m15 0-3-3m3 3-3 3m-9-3 3-3m-3 3 3 3" />
                                            </svg>
                                            <span>Pengajuan Sidang</span>
                                            <x-ui.lock-indicator :show="$showMahasiswaLock" :message="$mahasiswaLockInfo" />
                                        </div>
                                    </a>
                                </li>
                            @endif


                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="lg:pl-72">
        <div
            class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-xs sm:gap-x-6 sm:px-6 lg:px-8">
            <button type="button" command="show-modal" commandfor="sidebar"
                class="-m-2.5 p-2.5 text-gray-700 hover:text-gray-900 lg:hidden">
                <span class="sr-only">Open sidebar</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon"
                    aria-hidden="true" class="size-6">
                    <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>

            <!-- Separator -->
            <div aria-hidden="true" class="h-6 w-px bg-gray-200 lg:hidden"></div>

            <div class="flex justify-end flex-1 gap-x-4 self-stretch lg:gap-x-6">

                <div class="flex items-center gap-x-4 lg:gap-x-6">


                    <!-- Profile dropdown -->
                    <el-dropdown class="relative">
                        @php
                            $profilePhoto =
                                Auth::user()->photo ??
                                (Auth::user()->mahasiswa?->photo ?? (Auth::user()->dosen?->photo ?? null));
                            $profileRoute = $isAdmin
                                ? route('admin.profile')
                                : ($isKaprodi
                                    ? route('kaprodi.profile')
                                    : ($isDosen
                                        ? route('dosen.profile')
                                        : route('mahasiswa.profile')));
                            $roleLabel = strtoupper((string) Auth::user()->getRoleNames()->first());
                            $roleName = $isAdmin
                                ? 'Admin'
                                : ($isKaprodi
                                    ? 'Pimpinan / Kaprodi'
                                    : ($isDosen
                                        ? 'Dosen'
                                        : 'Mahasiswa'));
                        @endphp
                        <button
                            class="relative flex items-center gap-3 rounded-full border border-slate-200 bg-white px-2.5 py-1.5 shadow-sm transition hover:border-slate-300 hover:shadow-md">
                            <span class="absolute -inset-1.5"></span>
                            <span class="sr-only">Open user menu</span>
                            @if ($profilePhoto)
                                <img src="{{ Storage::url($profilePhoto) }}" alt=""
                                    class="size-10 rounded-full object-cover ring-2 ring-slate-100" />
                            @else
                                <div
                                    class="flex size-10 items-center justify-center rounded-full bg-linear-to-br from-blue-100 to-cyan-100 text-xs font-semibold text-blue-700 ring-2 ring-slate-100">
                                    {{ Auth::user()->initials() }}
                                </div>
                            @endif
                            <span class="hidden lg:flex lg:items-center">
                                <span class="ml-1 text-left">
                                    <span aria-hidden="true"
                                        class="block text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</span>
                                    <span
                                        class="block text-xs uppercase tracking-[0.2em] text-slate-400">{{ $roleLabel }}</span>
                                </span>
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="ml-2 size-5 text-slate-400">
                                    <path
                                        d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                        <el-menu anchor="bottom end" popover
                            class="w-72 origin-top-right rounded-3xl border border-slate-200 bg-white p-3 shadow-2xl shadow-slate-200/60 outline-hidden transition transition-discrete [--anchor-gap:--spacing(3)] data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in">
                            <div
                                class="rounded-2xl bg-linear-to-br from-slate-900 via-slate-800 to-blue-900 p-4 text-white">
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-100/80">
                                    {{ $roleName }}</p>
                                <div class="mt-3">
                                    <p class="text-base font-semibold">{{ Auth::user()->name }}</p>
                                    <p class="text-sm text-white/70">{{ Auth::user()->email }}</p>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <a href="{{ $profileRoute }}" wire:navigate
                                    class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900">
                                    <span class="flex items-center gap-3">
                                        <span
                                            class="flex size-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </span>
                                        <span>
                                            <span class="block">Profil</span>
                                            <span class="block text-xs font-medium text-slate-400">Kelola akun dan
                                                password</span>
                                        </span>
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4 text-slate-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </a>

                                <livewire:auth.logout class="w-full">
                                    <span
                                        class="flex cursor-pointer items-center justify-between rounded-2xl px-3 py-3 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 hover:text-rose-700">
                                        <span class="flex items-center gap-3">
                                            <span
                                                class="flex size-10 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                                </svg>
                                            </span>
                                            <span>
                                                <span class="block">Keluar</span>
                                                <span class="block text-xs font-medium text-rose-300/90">Akhiri sesi
                                                    login saat ini</span>
                                            </span>
                                        </span>
                                    </span>
                                </livewire:auth.logout>
                            </div>
                        </el-menu>
                    </el-dropdown>
                </div>
            </div>
        </div>

        <main class="py-10">
            <div class="px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>

</div>
