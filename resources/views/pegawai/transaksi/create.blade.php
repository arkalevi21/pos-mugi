@extends('layouts.app')

@section('title', 'Transaksi Kasir')
@section('header-title', 'Kasir')

@section('content')
<div class="sticky top-16 z-30 bg-gray-50 pt-2 pb-4 -mx-4 px-4 overflow-x-auto whitespace-nowrap hide-scrollbar border-b border-gray-200 mb-4">
    <a href="{{ route('transaksi.create') }}" 
       class="inline-block px-4 py-2 rounded-full text-sm font-medium transition-colors mr-2
       {{ $categoryFilter == 'all' ? 'bg-warkop text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
        Semua
    </a>
    @foreach($kategori as $kat)
    <a href="{{ route('transaksi.create', ['kategori' => $kat->id_kategori]) }}" 
       class="inline-block px-4 py-2 rounded-full text-sm font-medium transition-colors mr-2
       {{ $categoryFilter == $kat->id_kategori ? 'bg-warkop text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
        {{ $kat->nama_kategori }}
    </a>
    @endforeach
</div>

@if(session('success'))
<div class="bg-green-50 border-l-4 border-green-500 p-3 rounded mb-4 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-2 text-green-700 text-sm">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    <button onclick="this.parentElement.remove()" class="text-green-700"><i class="fa-solid fa-xmark"></i></button>
</div>
@endif

@if(session('error'))
<div class="bg-red-50 border-l-4 border-red-500 p-3 rounded mb-4 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-2 text-red-700 text-sm">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
    <button onclick="this.parentElement.remove()" class="text-red-700"><i class="fa-solid fa-xmark"></i></button>
</div>
@endif

<div class="grid grid-cols-2 gap-3 pb-24">
    @forelse($product as $item)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full hover:shadow-md transition-shadow cursor-pointer relative group"
         onclick="document.getElementById('form-add-{{ $item->id_produk }}').submit();">

        <div class="p-3 flex-1 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-gray-800 text-sm line-clamp-2 leading-snug mb-1">{{ $item->nama_produk }}</h3>
                <p class="text-xs text-gray-500">{{ $item->kategori->nama_kategori }}</p>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <span class="text-warkop font-bold text-sm">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 group-hover:bg-warkop group-hover:text-white transition-colors">
                    <i class="fa-solid fa-plus text-xs"></i>
                </div>
            </div>
        </div>

        <form id="form-add-{{ $item->id_produk }}" action="{{ route('cart.add') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="id_produk" value="{{ $item->id_produk }}">
            <input type="hidden" name="qty" value="1">
        </form>
    </div>
    @empty
    <div class="col-span-2 py-10 text-center text-gray-400">
        <i class="fa-solid fa-magnifying-glass text-4xl mb-3 block"></i>
        <p class="text-sm">Produk tidak ditemukan.</p>
    </div>
    @endforelse
</div>

@if(count($cartItems) > 0)
<div class="fixed bottom-6 left-0 right-0 z-30 px-4 max-w-lg mx-auto pointer-events-none">
    <button onclick="toggleCartDrawer(true)" 
            class="pointer-events-auto w-full bg-gray-900 text-white p-4 rounded-xl shadow-2xl flex items-center justify-between hover:bg-gray-800 transition-transform active:scale-95 group">
        <div class="flex items-center gap-3">
            <div class="relative">
                <i class="fa-solid fa-cart-shopping text-xl"></i>
                <span class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full border border-gray-900">
                    {{ array_sum(array_column($cartItems, 'qty')) }}
                </span>
            </div>
            <div class="text-left flex flex-col">
                <span class="text-[10px] text-gray-400 uppercase font-semibold">Total Belanja</span>
                <span class="font-bold text-lg leading-none">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="flex items-center gap-2 text-sm font-medium text-gray-300 group-hover:text-white">
            Lihat <i class="fa-solid fa-chevron-up"></i>
        </div>
    </button>
</div>
@endif

<div id="cartBackdrop" onclick="toggleCartDrawer(false)" class="fixed inset-0 bg-black/60 z-50 hidden transition-opacity opacity-0 backdrop-blur-sm"></div>

<div id="cartDrawer" class="fixed bottom-0 left-0 right-0 z-[60] bg-white rounded-t-3xl shadow-[0_-5px_30px_rgba(0,0,0,0.2)] transform translate-y-full transition-transform duration-300 flex flex-col max-h-[90vh] max-w-lg mx-auto">
    
    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white rounded-t-3xl shrink-0">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-bag-shopping text-warkop"></i> Rincian Pesanan
        </h2>
        <div class="flex gap-2">
            <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan keranjang?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-red-500 hover:bg-red-50 px-3 py-1.5 rounded-full transition-colors font-medium border border-transparent hover:border-red-100">
                    Reset
                </button>
            </form>
            <button onclick="toggleCartDrawer(false)" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    </div>

    <div class="overflow-y-auto p-5 space-y-6 flex-1">
        <div class="space-y-3">
            @foreach($cartItems as $item)
            <div class="flex items-start gap-3 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                <div class="flex-1">
                    <div class="font-medium text-gray-800 text-sm">{{ $item['nama_product'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">@ Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</div>
                </div>
                
                <div class="flex flex-col items-end gap-2">
                    <div class="text-sm font-bold text-gray-800">
                        {{ number_format($item['subtotal'], 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-1">
                        <form action="{{ route('cart.remove', $item['id_product']) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors text-xs">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        
                        <div class="w-px h-4 bg-gray-200"></div>

                        <form action="{{ route('cart.update', $item['id_product']) }}" method="POST" class="flex items-center">
                            @csrf @method('PUT')
                            <input type="number" name="qty" value="{{ $item['qty'] }}" min="1" 
                                   class="w-8 text-center bg-transparent border-none text-xs p-0 focus:ring-0 font-bold text-gray-700">
                            <button type="submit" class="text-warkop hover:text-red-700 ml-1">
                                <i class="fa-solid fa-check text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Pembayaran</h3>
            
            <form method="POST" action="{{ route('transaksi.store') }}">
                @csrf
                <input type="hidden" name="total_harga" value="{{ $cartTotal }}">

                <div class="mb-3">
                    <label class="text-xs font-medium text-gray-700 mb-1 block">Nama Pelanggan</label>
                    <input type="text" name="nama_pembeli" value="{{ old('nama_pembeli', 'Pelanggan') }}" required
                           class="w-full text-sm rounded-lg border-gray-200 focus:border-warkop focus:ring-warkop py-2 px-3">
                </div>

                <div class="mb-3">
                    <label class="text-xs font-medium text-gray-700 mb-1 block">Metode</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer relative">
                            <input type="radio" name="metode_pembayaran" value="tunai" class="peer sr-only" id="bayarTunai" checked>
                            <div class="text-center py-2 px-2 rounded-lg border border-gray-200 bg-white text-gray-600 peer-checked:border-warkop peer-checked:bg-red-50 peer-checked:text-warkop transition-all">
                                <span class="text-xs font-bold"><i class="fa-solid fa-money-bill mr-1"></i> Tunai</span>
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" name="metode_pembayaran" value="qris" class="peer sr-only" id="bayarQris">
                            <div class="text-center py-2 px-2 rounded-lg border border-gray-200 bg-white text-gray-600 peer-checked:border-warkop peer-checked:bg-red-50 peer-checked:text-warkop transition-all">
                                <span class="text-xs font-bold"><i class="fa-solid fa-qrcode mr-1"></i> QRIS</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="inputTunaiArea" class="mb-4">
                    <label class="text-xs font-medium text-gray-700 mb-1 block">Uang Diterima</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input type="number" id="uang_diterima" name="uang_diterima" 
                               value="{{ old('uang_diterima', $cartTotal) }}" min="{{ $cartTotal }}"
                               class="w-full pl-9 pr-3 py-2 text-right font-bold text-gray-800 rounded-lg border-gray-200 focus:border-warkop focus:ring-warkop text-sm shadow-sm">
                    </div>
                    <div class="flex justify-between items-center mt-2 text-xs">
                        <span class="text-gray-500">Kembalian:</span>
                        <span id="labelKembalian" class="font-bold text-green-600">Rp 0</span>
                    </div>
                </div>

                <div id="infoQrisArea" class="hidden mb-4 p-3 bg-blue-50 text-blue-700 rounded-lg text-xs flex gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    <p>QR Code akan muncul setelah tombol bayar ditekan.</p>
                </div>

                <button type="submit" class="w-full bg-warkop text-white font-bold py-3 rounded-xl shadow-lg hover:bg-red-800 active:scale-95 transition-all flex justify-between px-4 text-sm items-center">
                    <span>Bayar</span>
                    <span>Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection

@section('scripts')
<script>
    // Logic Drawer
    const drawer = document.getElementById('cartDrawer');
    const backdrop = document.getElementById('cartBackdrop');
    const body = document.body;

    function toggleCartDrawer(show) {
        if (show) {
            backdrop.classList.remove('hidden');
            setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
            drawer.classList.remove('translate-y-full');
            body.classList.add('overflow-hidden');
        } else {
            backdrop.classList.add('opacity-0');
            drawer.classList.add('translate-y-full');
            setTimeout(() => backdrop.classList.add('hidden'), 300);
            body.classList.remove('overflow-hidden');
        }
    }

    // Logic Hitung Kembalian & Toggle Metode
    document.addEventListener('DOMContentLoaded', function() {
        const radioTunai = document.getElementById('bayarTunai');
        const radioQris = document.getElementById('bayarQris');
        const areaTunai = document.getElementById('inputTunaiArea');
        const areaQris = document.getElementById('infoQrisArea');
        const inputUang = document.getElementById('uang_diterima');
        const labelKembalian = document.getElementById('labelKembalian');
        const totalBelanja = {{ $cartTotal }};

        function toggleMetode() {
            if (radioTunai.checked) {
                areaTunai.classList.remove('hidden');
                areaQris.classList.add('hidden');
                if(inputUang) inputUang.required = true;
                hitungKembalian(); 
            } else {
                areaTunai.classList.add('hidden');
                areaQris.classList.remove('hidden');
                if(inputUang) inputUang.required = false;
            }
        }

        function hitungKembalian() {
            if(!inputUang) return;
            let uangMasuk = parseInt(inputUang.value) || 0;
            let kembalian = uangMasuk - totalBelanja;

            if (kembalian >= 0) {
                labelKembalian.innerText = 'Rp ' + kembalian.toLocaleString('id-ID');
                labelKembalian.className = 'font-bold text-green-600';
            } else {
                labelKembalian.innerText = 'Kurang: Rp ' + Math.abs(kembalian).toLocaleString('id-ID');
                labelKembalian.className = 'font-bold text-red-500';
            }
        }

        if(radioTunai && radioQris) {
            radioTunai.addEventListener('change', toggleMetode);
            radioQris.addEventListener('change', toggleMetode);
            if(inputUang) inputUang.addEventListener('input', hitungKembalian);
            toggleMetode(); // init
        }

        // Auto Open jika ada error validasi
        @if($errors->any() || session('cart_open'))
            toggleCartDrawer(true);
        @endif
    });
</script>
@endsection