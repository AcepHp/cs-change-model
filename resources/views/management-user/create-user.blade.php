<x-app-layout>
    @include('components.partials.toast')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah User') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('user.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block font-medium text-gray-700">Nama Lengkap*</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-lg w-full p-2 shadow-sm">
                            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700">NPK*</label>
                            <input type="text" name="npk" value="{{ old('npk') }}" required
                                class="border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-lg w-full p-2 shadow-sm">
                            @error('npk') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-lg w-full p-2 shadow-sm">
                            @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700">Role*</label>
                            <select name="role" required
                                class="border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-lg w-full p-2 shadow-sm">
                                <option value="">Pilih Role</option>
                                <option value="produksi" {{ old('role') == 'produksi' ? 'selected' : '' }}>Produksi</option>
                                <option value="quality" {{ old('role') == 'quality' ? 'selected' : '' }}>Quality</option>
                            </select>
                            @error('role') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700">Password*</label>
                            <input type="password" name="password" required
                                class="border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-lg w-full p-2 shadow-sm">
                            @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Konfirmasi Password*</label>
                            <input type="password" name="password_confirmation" required
                                class="border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-lg w-full p-2 shadow-sm">
                        </div>



                        <div class="flex justify-end gap-3 items-center pt-4">
                            <a href="{{ route('managementUser') }}">
                                <u>Kembali</u>
                            </a>

                            <x-primary-button>SIMPAN USER</x-primary-button>

                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>