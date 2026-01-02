@extends('layouts.app')

@section('title', 'Kelola Kategori')
@section('header-title', 'Data Kategori')

@section('content')

{{-- Alert Section (Tetap sama) --}}
<div class="space-y-4 mb-6">
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm flex items-center justify-between animate-fade-in-down">
        <div class="flex items-center gap-3 text-green-700">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900"><i class="fa-solid fa-xmark"></i></button>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm flex items-center justify-between animate-fade-in-down">
        <div class="flex items-center gap-3 text-red-700">
            <i class="fa-solid fa-circle-exclamation text-xl"></i>
            <span class="font-medium text-sm">{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900"><i class="fa-solid fa-xmark"></i></button>
    </div>
    @endif
</div>

@if($kategori->count() > 0)
{{-- PERUBAHAN UTAMA DI SINI --}}
{{-- Container utama dibuat putih dan rounded, item di dalamnya hanya list --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-24">
    @foreach($kategori as $kat)
    <div class="p-4 flex items-center justify-between border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors group relative">
        
        <div class="flex items-center gap-4 overflow-hidden">
            {{-- Icon dibuat sedikit lebih kecil agar pas dengan tampilan list --}}
            <div class="w-10 h-10 shrink-0 rounded-full bg-red-50 flex items-center justify-center text-warkop">
                <i class="fa-solid fa-layer-group text-sm"></i>
            </div>
            
            <div class="min-w-0">
                <h3 class="font-bold text-gray-800 text-sm truncate pr-2">
                    {{ $kat->nama_kategori }}
                </h3>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[10px] text-gray-500 font-medium">
                        {{ $kat->produk_count ?? 0 }} Produk
                    </span>
                    <span class="text-[10px] text-gray-300">|</span>
                    <span class="text-[10px] text-gray-400 font-mono">#{{ $kat->id_kategori }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 pl-2">
            <a href="{{ route('kategori.index', ['edit' => $kat->id_kategori]) }}" 
               class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-yellow-50 hover:text-yellow-600 hover:border-yellow-200 transition-all">
                <i class="fa-solid fa-pen text-xs"></i>
            </a>

            @if($kat->produk_count > 0)
                <button type="button" onclick="alert('Gagal: Kategori ini masih memiliki produk!')" 
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-300 cursor-not-allowed">
                    <i class="fa-solid fa-lock text-xs"></i>
                </button>
            @else
                <form action="{{ route('kategori.destroy', $kat->id_kategori) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $kat->nama_kategori }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </form>
            @endif
        </div>
        
    </div>
    @endforeach
</div>
@else
<div class="flex flex-col items-center justify-center py-20 text-center">
    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
        <i class="fa-solid fa-folder-open text-3xl"></i>
    </div>
    <h3 class="text-gray-900 font-bold text-lg mb-2">Belum Ada Kategori</h3>
    <p class="text-gray-500 text-sm max-w-xs mx-auto mb-6">Mulai dengan menambahkan kategori produk pertama Anda.</p>
</div>
@endif

{{-- Floating Action Button (Tetap sama) --}}
<button onclick="openModal('tambah')" 
        class="fixed bottom-6 right-6 z-40 w-14 h-14 bg-warkop text-white rounded-full shadow-xl hover:bg-red-800 hover:scale-110 transition-all flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-red-200 active:scale-90">
    <i class="fa-solid fa-plus text-2xl"></i>
</button>

{{-- Modal & Scripts (Tetap sama, tidak perlu diubah) --}}
<div id="modalBackdrop" class="fixed inset-0 bg-black/60 z-50 hidden transition-opacity opacity-0 backdrop-blur-sm flex items-center justify-center px-4">
    <div id="modalContent" class="bg-white w-full max-w-sm rounded-2xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 relative overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 id="modalTitle" class="font-bold text-gray-800 text-lg"></h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-6">
            <form id="kategoriForm" method="POST" action="">
                @csrf
                <div id="methodField"></div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Kategori</label>
                    <input type="text" id="inputNamaKategori" name="nama_kategori" required maxlength="100" placeholder="Contoh: Makanan Berat" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-warkop focus:ring-0 transition-colors font-semibold text-gray-800 placeholder-gray-400">
                </div>
                <button type="submit" class="w-full py-3.5 bg-warkop text-white rounded-xl font-bold text-base hover:bg-red-800 shadow-lg hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span id="btnSubmitText">Simpan</span>
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const modalBackdrop = document.getElementById('modalBackdrop');
    const modalContent = document.getElementById('modalContent');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('kategoriForm');
    const inputNama = document.getElementById('inputNamaKategori');
    const methodField = document.getElementById('methodField');
    const btnSubmitText = document.getElementById('btnSubmitText');

    function openModal(mode, data = null) {
        modalBackdrop.classList.remove('hidden');
        void modalBackdrop.offsetWidth; 
        modalBackdrop.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');

        if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Kategori';
            inputNama.value = data.nama_kategori;
            btnSubmitText.innerText = 'Update Perubahan';
            form.action = "{{ route('kategori.index') }}/" + data.id_kategori;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        } else {
            modalTitle.innerText = 'Kategori Baru';
            inputNama.value = '';
            btnSubmitText.innerText = 'Simpan Kategori';
            form.action = "{{ route('kategori.store') }}";
            methodField.innerHTML = '';
        }
        setTimeout(() => inputNama.focus(), 100);
    }

    function closeModal() {
        modalBackdrop.classList.add('opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modalBackdrop.classList.add('hidden');
            const url = new URL(window.location);
            if (url.searchParams.has('edit')) {
                url.searchParams.delete('edit');
                window.history.pushState({}, '', url);
            }
        }, 300);
    }

    @if(isset($kategoriEdit) && $kategoriEdit)
        document.addEventListener('DOMContentLoaded', function() {
            openModal('edit', {
                id_kategori: "{{ $kategoriEdit->id_kategori }}",
                nama_kategori: "{!! $kategoriEdit->nama_kategori !!}" 
            });
        });
    @endif

    @if($errors->any() && !request()->has('edit'))
         document.addEventListener('DOMContentLoaded', function() {
            openModal('tambah');
         });
    @endif
</script>
@endsection