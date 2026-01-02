@extends('layouts.app')

@section('title', 'Transaksi Baru')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-cart-plus"></i> Transaksi Baru
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="#cartSection" class="btn btn-primary me-2" data-bs-toggle="collapse">
            <i class="bi bi-cart"></i> Keranjang
            @if(count($cartItems) > 0)
            <span class="badge bg-danger">{{ array_sum(array_column($cartItems, 'qty')) }}</span>
            @endif
        </a>
        <form action="{{ route('cart.clear') }}" method="POST" class="d-inline" 
              onsubmit="return confirm('Yakin ingin mengosongkan keranjang?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash"></i> Kosongkan
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- CART SECTION -->
<div class="collapse {{ count($cartItems) > 0 ? 'show' : '' }} mb-4" id="cartSection">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="bi bi-cart-check"></i> Keranjang Belanja
                @if(count($cartItems) > 0)
                <span class="badge bg-light text-dark ms-2">
                    {{ array_sum(array_column($cartItems, 'qty')) }} item
                </span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            @if(count($cartItems) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Produk</th>
                            <th width="100">Harga</th>
                            <th width="120">Qty</th>
                            <th width="120">Subtotal</th>
                            <th width="80" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $index => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $item['nama_produk'] }}</strong>
                                <br>
                                <small class="text-muted">{{ $item['nama_kategori'] }}</small>
                            </td>
                            <td class="text-nowrap">
                                Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}
                            </td>
                            <td>
                                <form action="{{ route('cart.update', $item['id_produk']) }}" method="POST" class="d-flex gap-1">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="qty" value="{{ $item['qty'] }}" min="1" 
                                           class="form-control form-control-sm" style="width: 70px;">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="fw-bold text-primary">
                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <form action="{{ route('cart.remove', $item['id_produk']) }}" method="POST" 
                                      onsubmit="return confirm('Hapus {{ $item['nama_produk'] }} dari keranjang?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total:</td>
                            <td class="fw-bold text-primary fs-5">
                                Rp {{ number_format($cartTotal, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- CHECKOUT FORM -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-cash-coin"></i> Checkout
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('transaksi.store') }}">
                        @csrf
                        <input type="hidden" name="total_harga" value="{{ $cartTotal }}">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_pembeli" class="form-label">Nama Pembeli</label>
                                    <input type="text" class="form-control @error('nama_pembeli') is-invalid @enderror" 
                                           id="nama_pembeli" name="nama_pembeli" 
                                           value="{{ old('nama_pembeli', 'Pelanggan') }}" required>
                                    @error('nama_pembeli')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                    <select class="form-select @error('metode_pembayaran') is-invalid @enderror" 
                                            id="metode_pembayaran" name="metode_pembayaran" required>
                                        <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                        <option value="qris" {{ old('metode_pembayaran') == 'qris' ? 'selected' : '' }}>QRIS</option>
                                    </select>
                                    @error('metode_pembayaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- TUNAI FIELDS -->
                        <div id="tunaiFields" class="{{ old('metode_pembayaran') == 'qris' ? 'd-none' : '' }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="uang_diterima" class="form-label">Uang Diterima</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control @error('uang_diterima') is-invalid @enderror" 
                                                   id="uang_diterima" name="uang_diterima" 
                                                   value="{{ old('uang_diterima', $cartTotal) }}" min="{{ $cartTotal }}">
                                        </div>
                                        @error('uang_diterima')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kembalian</label>
                                        <div class="alert alert-info">
                                            <div class="row">
                                                <div class="col-6">Total:</div>
                                                <div class="col-6 text-end">Rp {{ number_format($cartTotal, 0, ',', '.') }}</div>
                                                <div class="col-6">Kembalian:</div>
                                                <div class="col-6 text-end text-success" id="kembalianDisplay">
                                                    Rp 0
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- QRIS FIELDS -->
                        <div id="qrisFields" class="{{ old('metode_pembayaran') == 'qris' ? '' : 'd-none' }}">
                            <div class="alert alert-warning">
                                <i class="bi bi-qr-code-scan"></i>
                                Pembayaran QRIS akan diproses menggunakan Midtrans
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="fw-bold text-primary fs-5">
                                Total: Rp {{ number_format($cartTotal, 0, ',', '.') }}
                            </div>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Proses Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                <p class="mt-3">Keranjang kosong</p>
                <p class="small">Pilih produk dari daftar di bawah untuk mulai berbelanja</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- PRODUCT CATEGORY FILTER -->
<div class="card mb-3">
    <div class="card-body">
        <h6><i class="bi bi-filter"></i> Filter Kategori:</h6>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('transaksi.create') }}" 
               class="btn btn-sm {{ $categoryFilter == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                Semua
            </a>
            @foreach($kategori as $kat)
            <a href="{{ route('transaksi.create', ['kategori' => $kat->id_kategori]) }}" 
               class="btn btn-sm {{ $categoryFilter == $kat->id_kategori ? 'btn-info' : 'btn-outline-info' }}">
                {{ $kat->nama_kategori }}
            </a>
            @endforeach
        </div>
    </div>
</div>

<!-- PRODUCT GRID -->
<div class="row">
    @forelse($produk as $item)
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <h6 class="card-title">{{ $item->nama_produk }}</h6>
                <p class="card-text text-muted small mb-2">{{ $item->kategori->nama_kategori }}</p>
                <p class="card-text fw-bold text-primary mb-3">
                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                </p>
                
                <!-- ADD TO CART FORM -->
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_produk" value="{{ $item->id_produk }}">
                    <div class="input-group input-group-sm mb-2">
                        <button class="btn btn-outline-secondary" type="button" onclick="changeQty(this, -1)">-</button>
                        <input type="number" class="form-control text-center" name="qty" value="1" min="1" id="qty_{{ $item->id_produk }}">
                        <button class="btn btn-outline-secondary" type="button" onclick="changeQty(this, 1)">+</button>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-warning text-center py-4">
            <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
            <p class="mt-3">Tidak ada produk dalam kategori ini</p>
            <a href="{{ route('transaksi.create') }}" class="btn btn-outline-primary">
                Lihat Semua Produk
            </a>
        </div>
    </div>
    @endforelse
</div>
@endsection

@section('scripts')
<script>
    // Toggle payment method fields
    document.getElementById('metode_pembayaran').addEventListener('change', function() {
        const tunaiFields = document.getElementById('tunaiFields');
        const qrisFields = document.getElementById('qrisFields');
        
        if (this.value === 'tunai') {
            tunaiFields.classList.remove('d-none');
            qrisFields.classList.add('d-none');
            document.getElementById('uang_diterima').required = true;
        } else {
            tunaiFields.classList.add('d-none');
            qrisFields.classList.remove('d-none');
            document.getElementById('uang_diterima').required = false;
        }
        calculateChange();
    });
    
    // Calculate change for cash payment
    function calculateChange() {
        const total = {{ $cartTotal }};
        const uangDiterima = document.getElementById('uang_diterima').value || 0;
        const kembalian = uangDiterima - total;
        const display = document.getElementById('kembalianDisplay');
        
        if (kembalian >= 0) {
            display.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');
            display.classList.remove('text-danger');
            display.classList.add('text-success');
        } else {
            display.textContent = 'Rp 0';
            display.classList.remove('text-success');
            display.classList.add('text-danger');
        }
    }
    
    // Update quantity input
    function changeQty(button, change) {
        const input = button.parentElement.querySelector('input[name="qty"]');
        let currentQty = parseInt(input.value);
        let newQty = currentQty + change;
        
        if (newQty < 1) newQty = 1;
        
        input.value = newQty;
    }
    
    // Calculate change on input
    document.getElementById('uang_diterima')?.addEventListener('input', calculateChange);
    
    // Auto expand cart section if cart has items
    @if(count($cartItems) > 0)
    document.addEventListener('DOMContentLoaded', function() {
        calculateChange();
    });
    @endif
</script>
@endsection