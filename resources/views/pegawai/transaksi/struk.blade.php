<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $transaksi->id_transaksi }}</title>
    <style>
        @media print {
            body * { visibility: hidden; }
            #struk, #struk * { visibility: visible; }
            #struk { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
        body { font-family: 'Courier New', monospace; }
        .struk-container { max-width: 300px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; }
        .item-row { border-bottom: 1px dashed #ccc; padding: 5px 0; }
        .total-row { border-top: 2px solid #000; padding-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="text-center mb-3 no-print">
            <h3><i class="bi bi-printer"></i> Cetak Struk</h3>
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="bi bi-printer"></i> Cetak
            </button>
            <a href="{{ route('transaksi.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Transaksi Baru
            </a>
        </div>
        
        <div id="struk" class="struk-container">
            <!-- HEADER -->
            <div class="header mb-3">
                <h4 class="mb-1">TOKO MUGI</h4>
                <p class="mb-1">Jl. Contoh No. 123</p>
                <p class="mb-1">Telp: 0812-3456-7890</p>
                <p>================================</p>
            </div>
            
            <!-- TRANSAKSI INFO -->
            <div class="mb-3">
                <p class="mb-1">No: <strong>#{{ str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT) }}</strong></p>
                <p class="mb-1">Kasir: {{ $transaksi->user->nama_user }}</p>
                <p class="mb-1">Tanggal: {{ $transaksi->tanggal->format('d/m/Y H:i') }}</p>
                <p class="mb-1">Pembeli: {{ $transaksi->nama_pembeli }}</p>
                <p>--------------------------------</p>
            </div>
            
            <!-- ITEMS -->
            <div class="mb-3">
                @foreach($transaksi->detailTransaksi as $item)
                <div class="item-row">
                    <div class="d-flex justify-content-between">
                        <span>{{ $item->produk->nama_produk }}</span>
                        <span>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ $item->qty }} x</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
                <p>--------------------------------</p>
            </div>
            
            <!-- TOTAL -->
            <div class="mb-3">
                <div class="d-flex justify-content-between total-row">
                    <span>TOTAL:</span>
                    <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                </div>
                
                @if($transaksi->metode_pembayaran == 'tunai')
                <div class="d-flex justify-content-between">
                    <span>TUNAI:</span>
                    <span>Rp {{ number_format($transaksi->uang_diterima, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>KEMBALI:</span>
                    <span>Rp {{ number_format($transaksi->uang_kembalian, 0, ',', '.') }}</span>
                </div>
                @else
                <div class="d-flex justify-content-between">
                    <span>METODE:</span>
                    <span>QRIS</span>
                </div>
                @endif
                <p>================================</p>
            </div>
            
            <!-- FOOTER -->
            <div class="text-center mt-4">
                <p class="mb-1">Terima kasih atas kunjungannya</p>
                <p class="mb-1">Barang yang sudah dibeli tidak dapat</p>
                <p>ditukar/dikembalikan</p>
            </div>
        </div>
    </div>
    
    <script>
        // Auto print
        window.onload = function() {
            @if(request()->has('auto_print'))
            window.print();
            @endif
        };
    </script>
</body>
</html>