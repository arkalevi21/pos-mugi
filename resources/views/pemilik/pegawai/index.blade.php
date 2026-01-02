@extends('layouts.app')

@section('title', 'Kelola Data Pegawai')
@section('header-title', 'Data Pegawai')

@section('content')

    @if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200 shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200 shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="mb-4 relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <i class="fa-solid fa-magnifying-glass"></i>
        </span>
        <input type="text" placeholder="Cari pegawai..." class="w-full bg-gray-50 border border-gray-200 text-sm rounded-lg pl-10 pr-4 py-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <div class="flex flex-col">
        @forelse($pegawai as $user)
        <div class="group flex justify-between items-center py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors px-1 -mx-1 rounded-md">
            
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr($user->nama_user, 0, 2)) }}
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm leading-tight">
                        {{ $user->nama_user }}
                    </h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500 border border-gray-200">
                            {{ ucfirst($user->role) }}
                        </span>
                        @if(auth()->id() == $user->id_user)
                            <span class="text-[10px] font-bold text-blue-500 bg-blue-50 px-1.5 rounded">ANDA</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" 
                    onclick="openEditModal(this)"
                    data-user="{{ json_encode($user) }}"
                    data-url="{{ route('pemilik.pegawai.update', $user->id_user) }}"
                    class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded text-gray-500 hover:bg-white hover:text-blue-600 hover:border-blue-300 transition-all shadow-sm {{ auth()->id() == $user->id_user ? 'opacity-40 cursor-not-allowed' : '' }}"
                    {{ auth()->id() == $user->id_user ? 'disabled' : '' }}>
                    <i class="fa-regular fa-pen-to-square text-sm"></i>
                </button>

                <form action="{{ route('pemilik.pegawai.destroy', $user->id_user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pegawai {{ $user->nama_user }}?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-8 h-8 flex items-center justify-center border border-red-200 rounded text-red-500 hover:bg-red-50 hover:border-red-300 transition-all shadow-sm {{ auth()->id() == $user->id_user ? 'opacity-40 cursor-not-allowed' : '' }}"
                        {{ auth()->id() == $user->id_user ? 'disabled' : '' }}>
                        <i class="fa-regular fa-trash-can text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-12 flex flex-col items-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                <i class="fa-solid fa-users-slash text-gray-300 text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm">Belum ada data pegawai.</p>
        </div>
        @endforelse
    </div>

    <div class="fixed bottom-6 right-6 z-40">
        <button onclick="toggleModal('addModal')" 
            class="flex items-center gap-2 bg-[#a52a2a] hover:bg-[#8b2323] text-white pl-4 pr-5 py-3 rounded-full shadow-lg hover:shadow-xl transition-all active:scale-95 font-medium text-sm">
            <i class="fa-solid fa-plus text-lg"></i>
            <span>Tambah</span>
        </button>
    </div>

    <div id="addModal" class="relative z-[60] {{ $errors->hasAny(['nama_user', 'username', 'password', 'role']) ? '' : 'hidden' }}" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" onclick="toggleModal('addModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all w-full max-w-sm">
                    <div class="bg-white px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-base font-semibold text-gray-900">Tambah Pegawai Baru</h3>
                        <button type="button" onclick="toggleModal('addModal')" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <form action="{{ route('pemilik.pegawai.store') }}" method="POST" class="p-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Nama Lengkap</label>
                            <input type="text" name="nama_user" value="{{ old('nama_user') }}" class="w-full rounded-lg border {{ $errors->has('nama_user') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Contoh: Budi Santoso">
                            @error('nama_user') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" class="w-full rounded-lg border {{ $errors->has('username') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Tanpa spasi">
                            @error('username') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Role</label>
                            <select name="role" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Password</label>
                            <input type="password" name="password" class="w-full rounded-lg border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>

                        <div class="pt-2 flex flex-row-reverse gap-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg bg-[#a52a2a] px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#8b2323] transition-colors">Simpan</button>
                            <button type="button" onclick="toggleModal('addModal')" class="w-full inline-flex justify-center rounded-lg bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="relative z-[60] hidden">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" onclick="toggleModal('editModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all w-full max-w-sm">
                    <div class="bg-white px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-base font-semibold text-gray-900">Edit Pegawai</h3>
                        <button type="button" onclick="toggleModal('editModal')" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    
                    <form id="formEditPegawai" action="#" method="POST" class="p-5 space-y-4">
                        @csrf
                        @method('PUT') <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Nama Lengkap</label>
                            <input type="text" id="edit_nama_user" name="nama_user" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Username</label>
                            <input type="text" id="edit_username" name="username" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Role</label>
                            <select id="edit_role" name="role" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm bg-white">
                                <option value="kasir">Kasir</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Password Baru (Opsional)</label>
                            <input type="password" name="password" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm placeholder-gray-300" placeholder="Kosongkan jika tetap">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
                        </div>

                        <div class="pt-2 flex flex-row-reverse gap-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg bg-[#a52a2a] px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-600 transition-colors">Update</button>
                            <button type="button" onclick="toggleModal('editModal')" class="w-full inline-flex justify-center rounded-lg bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    // JS HANYA UNTUK UI (Buka/Tutup Modal & Isi Data Edit)
    // Tidak ada request ke server sama sekali di sini.

    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

    function openEditModal(button) {
        // Ambil data dari atribut tombol
        const user = JSON.parse(button.getAttribute('data-user'));
        const updateUrl = button.getAttribute('data-url');

        // Isi form edit
        document.getElementById('formEditPegawai').action = updateUrl;
        document.getElementById('edit_nama_user').value = user.nama_user;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_role').value = user.role;
        
        // Buka modal
        toggleModal('editModal');
    }
</script>
@endsection