@extends('layouts.app')

@section('title', 'Kelola Produk')
@section('header-title', 'Data Produk')

@section('content')

{{-- 1. BAGIAN FILTER KATEGORI (TABS - JAVASCRIPT) --}}
<div class="mb-6 border-b border-gray-200">
    <div class="flex overflow-x-auto no-scrollbar gap-6 pb-0.5" id="categoryTabs">
        {{-- Tombol "Semua" --}}
        <button onclick="filterProduk('all', this)" 
           class="category-tab whitespace-nowrap pb-3 text-sm font-bold border-b-2 transition-colors border-warkop text-warkop focus:outline-none">
            Semua
        </button>

        {{-- Loop Kategori --}}
        @foreach($kategori as $kat)
            <button onclick="filterProduk('{{ $kat->id_kategori }}', this)" 
               class="category-tab whitespace-nowrap pb-3 text-sm font-bold border-b-2 transition-colors border-transparent text-gray-500 hover:text-gray-700 focus:outline-none">
                {{ $kat->nama_kategori }}
            </button>
        @endforeach
    </div>
</div>

{{-- 2. ALERT SECTION --}}
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

{{-- 3. PRODUCT LIST SECTION --}}
@if($produk->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-24 min-h-[300px]">
        
        {{-- Loop Produk --}}
        @foreach($produk as $prod)
        {{-- Tambahkan data-kategori di sini --}}
        <div class="product-item p-4 flex items-center justify-between border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors group"
             data-kategori="{{ $prod->id_kategori }}">
            
            {{-- BAGIAN KIRI --}}
            <div class="flex items-center gap-4 overflow-hidden">

                <div class="min-w-0">
                    <h3 class="font-bold text-gray-800 text-sm truncate pr-2">{{ $prod->nama_produk }}</h3>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-sm font-bold text-warkop">Rp{{ number_format($prod->harga, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-gray-300">|</span>
                        <span class="text-[10px] text-gray-500 truncate">{{ $prod->kategori->nama_kategori ?? 'Tanpa Kategori' }}</span>
                    </div>
                </div>
            </div>

            {{-- BAGIAN KANAN --}}
            <div class="flex items-center gap-2 pl-2">
                <a href="javascript:void(0)" onclick='openModal("edit", @json($prod))' class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-yellow-50 hover:text-yellow-600 hover:border-yellow-200 transition-all">
                    <i class="fa-solid fa-pen text-xs"></i>
                </a>
                <form action="{{ route('produk.destroy', $prod->id_produk) }}" method="POST" onsubmit="return confirm('Hapus produk {{ $prod->nama_produk }}?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach

        {{-- Pesan Kosong (Hidden by default, shown by JS if filter returns 0 results) --}}
        <div id="emptyState" class="hidden flex-col items-center justify-center py-10 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3 text-gray-300">
                <i class="fa-solid fa-filter text-2xl"></i>
            </div>
            <p class="text-gray-400 text-sm">Tidak ada produk di kategori ini</p>
        </div>
    </div>
@else
    {{-- Tampilan Kosong Total (Database Kosong) --}}
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
            <i class="fa-solid fa-box-open text-3xl"></i>
        </div>
        <h3 class="text-gray-900 font-bold text-lg mb-2">Tidak Ada Produk</h3>
        <p class="text-gray-500 text-sm max-w-xs mx-auto">Belum ada produk. Silakan tambah produk baru.</p>
    </div>
@endif

{{-- FAB & MODAL TETAP SAMA (Bagian ini tidak berubah dari kode sebelumnya) --}}
<button onclick="openModal('tambah')" class="fixed bottom-6 right-6 z-40 w-14 h-14 bg-warkop text-white rounded-full shadow-xl hover:bg-red-800 hover:scale-110 transition-all flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-red-200 active:scale-90">
    <i class="fa-solid fa-plus text-2xl"></i>
</button>

<div id="modalBackdrop" class="fixed inset-0 bg-black/60 z-50 hidden transition-opacity opacity-0 backdrop-blur-sm flex items-center justify-center px-4">
    <div id="modalContent" class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 relative overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center sticky top-0 z-10">
            <h3 id="modalTitle" class="font-bold text-gray-800 text-lg">Produk Baru</h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-6">
            <form id="produkForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Produk</label>
                    <input type="text" id="inputNama" name="nama_produk" required maxlength="100" placeholder="Contoh: Kopi Susu" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-warkop focus:ring-0 transition-colors font-semibold text-gray-800 placeholder-gray-400">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kategori</label>
                        <select id="inputKategori" name="id_kategori" required class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-warkop focus:ring-0 transition-colors font-semibold text-gray-800 bg-white">
                            <option value="">Pilih...</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Harga (Rp)</label>
                        <input type="number" id="inputHarga" name="harga" required min="0" placeholder="15000" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-warkop focus:ring-0 transition-colors font-semibold text-gray-800 placeholder-gray-400">
                    </div>
                </div>
                <button type="submit" class="w-full py-3.5 bg-warkop text-white rounded-xl font-bold text-base hover:bg-red-800 shadow-lg hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span id="btnSubmitText">Simpan Produk</span>
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // --- FITUR FILTER JAVASCRIPT ---
    function filterProduk(kategoriId, btnElement) {
        const items = document.querySelectorAll('.product-item');
        const tabs = document.querySelectorAll('.category-tab');
        const emptyState = document.getElementById('emptyState');
        let visibleCount = 0;

        // 1. Update Tampilan Tab Aktif
        tabs.forEach(tab => {
            // Reset style ke default (abu-abu, border transparent)
            tab.classList.remove('border-warkop', 'text-warkop');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Set style aktif ke tombol yang diklik (merah, border merah)
        btnElement.classList.remove('border-transparent', 'text-gray-500');
        btnElement.classList.add('border-warkop', 'text-warkop');

        // 2. Filter Item
        items.forEach(item => {
            if (kategoriId === 'all' || item.getAttribute('data-kategori') == kategoriId) {
                item.style.display = 'flex'; // Kembalikan ke flex karena parent pakai flex
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // 3. Cek jika hasil filter kosong
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
            emptyState.classList.add('flex');
        } else {
            emptyState.classList.add('hidden');
            emptyState.classList.remove('flex');
        }
    }

    // --- LOGIC MODAL (SAMA SEPERTI SEBELUMNYA) ---
    const modalBackdrop = document.getElementById('modalBackdrop');
    const modalContent = document.getElementById('modalContent');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('produkForm');
    const inputNama = document.getElementById('inputNama');
    const inputKategori = document.getElementById('inputKategori');
    const inputHarga = document.getElementById('inputHarga');
    const methodField = document.getElementById('methodField');
    const btnSubmitText = document.getElementById('btnSubmitText');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const previewImage = document.getElementById('imagePreview');

    function openModal(mode, data = null) {
        modalBackdrop.classList.remove('hidden');
        void modalBackdrop.offsetWidth; 
        modalBackdrop.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');

        if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Produk';
            btnSubmitText.innerText = 'Simpan Perubahan';
            inputNama.value = data.nama_produk;
            inputKategori.value = data.id_kategori;
            inputHarga.value = data.harga;
            form.action = "{{ route('produk.index') }}/" + data.id_produk;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        } else {
            modalTitle.innerText = 'Produk Baru';
            btnSubmitText.innerText = 'Simpan Produk';
            form.reset();
            previewContainer.classList.add('hidden');
            form.action = "{{ route('produk.store') }}";
            methodField.innerHTML = '';
        }
    }

    function closeModal() {
        modalBackdrop.classList.add('opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modalBackdrop.classList.add('hidden');
        }, 300);
    }

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            modalBackdrop.classList.remove('hidden', 'opacity-0');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        });
    @endif
</script>
@endsection