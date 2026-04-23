<div>
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="bg-white border border-gray-200 w-100 px-6 pb-12 shadow-sm sm:rounded-lg sm:px-12">
            <div class="brand my-6">
                <div class="">
                    <img class="mx-auto h-16 w-auto" src="{{ asset('storage/Logo/unpari.jpg') }}" alt="Your Company">
                </div>
                <h2 class="mt-4 text-center text-xl font-bold tracking-tight text-gray-900">Sign Up</h2>
            </div>
            @error(session('error'))
                <p class="text-center text-sm/6 text-red-500">
                    {{ $message }}
                </p>
            @enderror
            <form wire:submit.prevent="store" class="space-y-6">
                @csrf
                <div>
                    <label for="name" class="block text-sm/6 font-medium text-gray-900">Nama Lengkap</label>
                    <div class="mt-2">
                        <input id="name" type="text" name="name" wire:model="name" required
                            autocomplete="name" placeholder="Masukan Nama Lengkap"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                </div>
                <div>
                    <label for="nim" class="block text-sm/6 font-medium text-gray-900">NIM</label>
                    <div class="mt-2">
                        <input id="nim" type="text" name="nim" wire:model="nim" required autocomplete="nim"
                            placeholder="Masukan NIM"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                </div>
                <div>
                    <label for="prodi" class="block text-sm/6 font-medium text-gray-900">Program Studi</label>
                    <div class="mt-2">
                        <input id="prodi" type="text" name="prodi" wire:model="prodi" required autocomplete="prodi"
                            placeholder="Masukan Program Studi"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                </div>
                <div>
                    <label for="angkatan" class="block text-sm/6 font-medium text-gray-900">Angkatan</label>
                    <div class="mt-2">
                        <select id="angkatan" name="angkatan" wire:model="angkatan" required
                            autocomplete="angkatan" placeholder="Masukan Angkatan"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                            <option value="">Pilih Angkatan</option>
                            <option value="2020">2020</option>
                            <option value="2021">2021</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm/6 font-medium text-gray-900">Email</label>
                    <div class="mt-2">
                        <input id="email" type="email" name="email" wire:model="email" required
                            autocomplete="email" placeholder="Masukan email atau nim"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm/6 font-medium text-gray-900">Kata Sandi</label>
                    <div class="mt-2">
                        <input id="password" type="password" name="password" wire:model="password"
                            placeholder="Masukan Kata Sandi" required autocomplete="current-password"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm/6 font-medium text-gray-900">Konfirmasi Kata
                        Sandi</label>
                    <div class="mt-2">
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            wire:model="password_confirmation" placeholder="Konfirmasi Kata Sandi" required
                            autocomplete="current-password"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                </div>
                <div>
                    <label for="photo" class="block text-sm/6 font-medium text-gray-900">Foto Profile</label>
                    <div class="mt-2">
                        <input id="photo" type="file" name="photo" wire:model="photo"
                            placeholder="Pilih Foto Profile" required autocomplete="current-password"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="w-24 h-24 rounded-full object-cover">
                    @endif
                </div>


                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign
                        in</button>
                </div>
                <div class="">
                    <p class="text-center text-sm/6 text-gray-500">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" wire:navigate
                            class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Masuk</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
