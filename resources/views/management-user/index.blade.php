<x-app-layout>
    {{-- Tambahkan style untuk x-cloak --}}
    <style>
    [x-cloak] {
        display: none !important;
    }
    </style>

    @include('components.partials.toast')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Management User') }}
            </h2>
            <a href="{{ route('user.create') }}">
                <x-primary-button>Tambah User</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div x-data="{ openModal: false, deleteUrl: '' }">
        {{-- Blur Background Saat Modal Aktif --}}
        <div x-show="openModal" x-cloak x-transition.opacity
            class="fixed inset-0 z-40 bg-black bg-opacity-30 backdrop-blur-sm"></div>

        {{-- Konten Utama --}}
        <div class="py-10 relative z-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6 text-gray-900"
                        x-effect="document.body.style.overflow = openModal ? 'hidden' : 'auto'">

                        {{-- Tabel --}}
                        <div class="overflow-x-auto border border-gray-200 rounded">
                            <div class="max-h-[400px] overflow-y-auto">
                                <table class="w-full text-sm text-left text-gray-700">
                                    <thead class="bg-gray-100 text-gray-800 text-sm uppercase sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-center w-5">No</th>
                                            <th class="px-4 py-3">Nama Lengkap</th>
                                            <th class="px-4 py-3">NPK</th>
                                            <th class="px-4 py-3">Email</th>
                                            <th class="px-4 py-3">Role</th>
                                            <th class="px-4 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $i => $user)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-3 text-center w-5">{{ $users->firstItem() + $i }}</td>
                                            <td class="p-3">{{ $user->name }}</td>
                                            <td class="p-3">{{ $user->npk }}</td>
                                            <td class="p-3">{{ $user->email }}</td>
                                            <td class="p-3">
                                                @php
                                                $roleColors = [
                                                'produksi' => 'bg-green-100 text-green-800',
                                                'quality' => 'bg-gray-100 text-blue-800',
                                                ];
                                                @endphp
                                                <span
                                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <div class="flex justify-center gap-2">
                                                    
                                                    <a href="{{ route('user.edit', $user->id) }}">
                                                        <x-primary-button
                                                            class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 me-2 text-sm">
                                                            Edit
                                                        </x-primary-button>
                                                    </a>
                                                    
                                                    <x-primary-button
                                                        @click="openModal = true; deleteUrl = '{{ route('user.destroy', $user->id) }}'"
                                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 text-sm">
                                                        Hapus
                                                    </x-primary-button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="p-3 border text-center text-gray-500">
                                                Belum ada data user.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Hapus --}}
        <div x-show="openModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div @click.away="openModal = false" class="bg-white rounded-xl shadow-lg max-w-lg w-[480px] mx-4 p-6 z-50">
                <h2 class="text-xl font-bold mb-4">Konfirmasi Hapus</h2>
                <p class="text-sm text-gray-600 mb-6">
                    Yakin ingin menghapus user ini? Data tidak dapat dipulihkan.
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="openModal = false"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
                        Batal
                    </button>
                    <form :action="deleteUrl" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>