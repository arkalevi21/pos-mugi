@extends('layouts.app')

@section('title', 'Pencatatan Pengeluaran')
@section('header-title', 'Pengeluaran')

@section('content')

{{-- 1. HEADER STATISTIK & FILTER --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form action="{{ route('pengeluaran.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        {{-- Bagian Kiri: Total Pengeluaran --}}
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Pengeluaran</p>
            <h2 class="text-2xl font-bold text-[#a52a2a]">
                Rp{{ number_format($totalPengeluaran, 0, ',', '.') }}
            </h2>
            <p class="text-[10px] text-gray-400 mt-1">
                {{ $pengeluaran->count() }} Transaksi
                @if(request('tanggal'))
                    pada {{ \Carbon\Carbon::parse(request('tanggal'))->format('d/m/Y') }}
                @else
                    hari ini
                @endif
            </p>
        </div>

        {{-- Bagian Kanan: Filter Tanggal --}}
        <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-lg border border-gray-200">
            <input type="date" name="tanggal" 
                   value="{{ request('tanggal', date('Y-m-d')) }}"
                   onchange="this.form.submit()"
                   class="bg-transparent border-0 text-sm font-bold text-gray-700 focus:ring-0 p-1">
            
            @if(request()->has('tanggal'))
                <a href="{{ route('pengeluaran.index') }}" class="w-8 h-8 flex items-center justify-center rounded-md bg-white text-red-500 shadow-sm hover:bg-red-50 transition-colors" title="Reset Filter">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </div>
    </form>
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

{{-- 3. LIST PENGELUARAN --}}
@if($pengeluaran->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-24">
        @foreach($pengeluaran as $p)
        <div class="p-4 flex items-center justify-between border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors group">
            
            {{-- BAGIAN KIRI: Icon & Info --}}
            <div class="flex items-center gap-4 overflow-hidden">

                <div class="min-w-0">
                    <h3 class="font-bold text-gray-800 text-sm truncate pr-2">
                        {{ $p->nama_pengeluaran }}
                    </h3>
                    <div class="flex flex-col mt-0.5">
                        <span class="text-xs font-bold text-[#a52a2a]">
                            - Rp{{ number_format($p->nominal, 0, ',', '.') }}
                        </span>
                        @if($p->keterangan)
                            <span class="text-[10px] text-gray-400 truncate mt-0.5">
                                {{ $p->keterangan }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- BAGIAN KANAN: Tombol Aksi --}}
            <div class="flex items-center gap-2 pl-2">
                {{-- Tombol Edit --}}
                <a href="javascript:void(0)" 
                   onclick='openModal("edit", @json($p))'
                   class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-yellow-50 hover:text-yellow-600 hover:border-yellow-200 transition-all">
                    <i class="fa-solid fa-pen text-xs"></i>
                </a>

                {{-- Tombol Hapus --}}
                <form action="{{ route('pengeluaran.destroy', $p->id_pengeluaran) }}" method="POST" onsubmit="return confirm('Hapus pengeluaran {{ $p->nama_pengeluaran }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </form>
            </div>
            
        </div>
        @endforeach
    </div>
@else
    {{-- Empty State --}}
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
            <i class="fa-solid fa-wallet text-3xl"></i>
        </div>
        <h3 class="text-gray-900 font-bold text-lg mb-2">Tidak Ada Pengeluaran</h3>
        <p class="text-gray-500 text-sm max-w-xs mx-auto">
            Tidak ada catatan pengeluaran pada tanggal ini.
        </p>
    </div>
@endif

{{-- 4. FAB (FLOATING ACTION BUTTON) --}}
<button onclick="openModal('tambah')" 
        class="fixed bottom-6 right-6 z-40 w-14 h-14 bg-[#a52a2a] text-white rounded-full shadow-xl hover:bg-red-700 hover:scale-110 transition-all flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-red-200 active:scale-90">
    <i class="fa-solid fa-plus text-2xl"></i>
</button>

{{-- 5. MODAL FORM --}}
<div id="modalBackdrop" class="fixed inset-0 bg-black/60 z-50 hidden transition-opacity opacity-0 backdrop-blur-sm flex items-center justify-center px-4">
    <div id="modalContent" class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 relative overflow-hidden">
        
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center sticky top-0 z-10">
            <h3 id="modalTitle" class="font-bold text-gray-800 text-lg">Catat Pengeluaran</h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6">
            <form id="pengeluaranForm" method="POST" action="">
                @csrf
                <div id="methodField"></div>

                {{-- Input Nama --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Pengeluaran</label>
                    <input type="text" id="inputNama" name="nama_pengeluaran" required maxlength="150"
                           placeholder="Contoh: Beli Es Batu"
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-red-500 focus:ring-0 transition-colors font-semibold text-gray-800 placeholder-gray-400">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    {{-- Input Nominal --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nominal (Rp)</label>
                        <input type="number" id="inputNominal" name="nominal" required min="0"
                               placeholder="50000"
                               class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-red-500 focus:ring-0 transition-colors font-semibold text-gray-800 placeholder-gray-400">
                    </div>
                    
                    {{-- Input Tanggal --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tanggal</label>
                        <input type="date" id="inputTanggal" name="tanggal" required
                               value="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-red-500 focus:ring-0 transition-colors font-semibold text-gray-800">
                    </div>
                </div>

                {{-- Input Keterangan --}}
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Keterangan (Opsional)</label>
                    <textarea id="inputKeterangan" name="keterangan" rows="2"
                              placeholder="Catatan tambahan..."
                              class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-red-500 focus:ring-0 transition-colors font-semibold text-gray-800 placeholder-gray-400 resize-none"></textarea>
                </div>

                <button type="submit" class="w-full py-3.5 bg-[#a52a2a] text-white rounded-xl font-bold text-base hover:bg-red-700 shadow-lg hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span id="btnSubmitText">Simpan Pengeluaran</span>
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
    const form = document.getElementById('pengeluaranForm');
    const inputNama = document.getElementById('inputNama');
    const inputNominal = document.getElementById('inputNominal');
    const inputTanggal = document.getElementById('inputTanggal');
    const inputKeterangan = document.getElementById('inputKeterangan');
    const methodField = document.getElementById('methodField');
    const btnSubmitText = document.getElementById('btnSubmitText');

    function openModal(mode, data = null) {
        modalBackdrop.classList.remove('hidden');
        void modalBackdrop.offsetWidth; 
        modalBackdrop.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');

        if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Pengeluaran';
            btnSubmitText.innerText = 'Update Data';
            
            // Isi form
            inputNama.value = data.nama_pengeluaran;
            inputNominal.value = data.nominal;
            inputTanggal.value = data.tanggal; // Pastikan format Y-m-d dari controller
            inputKeterangan.value = data.keterangan || '';
            
            form.action = "{{ route('pengeluaran.index') }}/" + data.id_pengeluaran;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        } else {
            modalTitle.innerText = 'Catat Pengeluaran';
            btnSubmitText.innerText = 'Simpan Pengeluaran';
            
            // Reset form tapi pertahankan tanggal hari ini/filter
            inputNama.value = '';
            inputNominal.value = '';
            inputKeterangan.value = '';
            // Jika ada filter tanggal di URL, gunakan itu sebagai default
            const urlParams = new URLSearchParams(window.location.search);
            const filterTanggal = urlParams.get('tanggal');
            if(filterTanggal) {
                inputTanggal.value = filterTanggal;
            } else {
                inputTanggal.value = "{{ date('Y-m-d') }}";
            }

            form.action = "{{ route('pengeluaran.store') }}";
            methodField.innerHTML = '';
        }
        
        // Auto focus ke nama
        setTimeout(() => inputNama.focus(), 100);
    }

    function closeModal() {
        modalBackdrop.classList.add('opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modalBackdrop.classList.add('hidden');
            // Hapus parameter edit dari URL jika ada
            const url = new URL(window.location);
            if (url.searchParams.has('edit')) {
                url.searchParams.delete('edit');
                window.history.pushState({}, '', url);
            }
        }, 300);
    }

    // Handle Errors / Edit Mode dari Session/URL
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            modalBackdrop.classList.remove('hidden', 'opacity-0');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        });
    @endif
    
    @if(request()->has('edit') && isset($pengeluaranEdit))
        document.addEventListener('DOMContentLoaded', function() {
            openModal('edit', @json($pengeluaranEdit));
        });
    @endif
</script>
@endsection