<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan {{ $startDate }} - {{ $endDate }}</title>
    <style>
        @media print {
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
            @page { size: A4; margin: 1cm; }
        }
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-box { border: 1px solid #ddd; padding: 15px; margin: 20px 0; }
        .total-row { background-color: #f8f9fa; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container mt-4 no-print">
        <div class="text-center mb-4">
            <h3><i class="bi bi-printer"></i> Cetak Laporan</h3>
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="bi bi-printer"></i> Cetak
            </button>
            <a href="{{ route('pemilik.laporan.index', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="print-area">
        <!-- HEADER -->
        <div class="header">
            <h2>TOKO MUGI</h2>
            <p>Jl. Contoh No. 123, Kota Contoh</p>
            <p>Telp: 0812-3456-7890 | Email: info@tokomugi.com</p>
            <h3>LAPORAN PENJUALAN</h3>
            <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - 
               {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
            <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
        </div>
        
        <!-- SUMMARY -->
        <div class="summary-box">
            <h4>Ringkasan</h4>
            <div class="row">
                <div class="col-6">
                    <p>Total Transaksi: <strong>{{ $transactions->count() }}</strong></p>
                    <p>Total Pendapatan: <strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></p>
                </div>
                <div class="col-6">
                    <p>Total Pengeluaran: <strong>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong></p>
                    <p>Laba Bersih: <strong>Rp {{ number_format($labaBersih, 0, ',', '.') }}</strong></p>
                </div>
            </div>
        </div>
        
        <!-- TRANSAKSI -->
        <h4>Daftar Transaksi Penjualan</h4>
        @if($transactions->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th width="30">#</th>
                    <th>No. Transaksi</th>
                    <th>Nama Pembeli</th>
                    <th>Kasir</th>
                    <th>Metode</th>
                    <th class="text-right">Total</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $trans)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>TRX-{{ str_pad($trans->id_transaksi, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $trans->nama_pembeli }}</td>
                    <td>{{ $trans->user->nama_user ?? '-' }}</td>
                    <td>{{ $trans->metode_pembayaran == 'tunai' ? 'Tunai' : 'QRIS' }}</td>
                    <td class="text-right">Rp {{ number_format($trans->total_harga, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($trans->tanggal)->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="total-row">
                <tr>
                    <td colspan="5" class="text-right"><strong>Total Pendapatan:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        @else
        <p class="text-center">Tidak ada transaksi pada periode ini</p>
        @endif
        
        <!-- PENGELUARAN -->
        <h4>Daftar Pengeluaran</h4>
        @if($pengeluaran->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th width="30">#</th>
                    <th>Nama Pengeluaran</th>
                    <th class="text-right">Nominal</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengeluaran as $peng)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $peng->nama_pengeluaran }}</td>
                    <td class="text-right">Rp {{ number_format($peng->nominal, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($peng->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $peng->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="total-row">
                <tr>
                    <td colspan="2" class="text-right"><strong>Total Pengeluaran:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
        @else
        <p class="text-center">Tidak ada pengeluaran pada periode ini</p>
        @endif
        
        <!-- FOOTER -->
        <div style="margin-top: 50px;">
            <div style="float: right; width: 300px; text-align: center;">
                <p>Mengetahui,</p>
                <br><br><br>
                <p><strong>Pemilik Toko</strong></p>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
    
    <script>
        // Auto print jika parameter ada
        @if(request()->has('auto_print'))
        window.onload = function() {
            window.print();
        };
        @endif
    </script>
</body>
</html>