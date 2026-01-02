@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-bar-chart"></i> Laporan Penjualan
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('pemilik.laporan.index', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" 
               class="btn btn-outline-primary {{ request('start_date') == date('Y-m-d') ? 'active' : '' }}">
                Hari Ini
            </a>
            @php
                $startOfWeek = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');
                $endOfWeek = \Carbon\Carbon::now()->endOfWeek()->format('Y-m-d');
            @endphp
            <a href="{{ route('pemilik.laporan.index', ['start_date' => $startOfWeek, 'end_date' => $endOfWeek]) }}" 
               class="btn btn-outline-primary {{ request('start_date') == $startOfWeek ? 'active' : '' }}">
                Minggu Ini
            </a>
            @php
                $startOfMonth = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                $endOfMonth = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
            @endphp
            <a href="{{ route('pemilik.laporan.index', ['start_date' => $startOfMonth, 'end_date' => $endOfMonth]) }}" 
               class="btn btn-outline-primary {{ request('start_date') == $startOfMonth ? 'active' : '' }}">
                Bulan Ini
            </a>
        </div>
        <a href="{{ route('pemilik.laporan.print', request()->all()) }}" 
           class="btn btn-success" target="_blank">
            <i class="bi bi-printer"></i> Cetak Laporan
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('pemilik.laporan.index') }}" class="row align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-range"></i>
                    </span>
                    <input type="date" class="form-control" name="start_date" 
                           value="{{ $startDate }}">
                    <span class="input-group-text">s/d</span>
                    <input type="date" class="form-control" name="end_date" 
                           value="{{ $endDate }}">
                    <button class="btn btn-primary" type="submit">
                        Filter
                    </button>
                </div>
            </div>
            <div class="col-md-8 text-end">
                <div class="d-flex justify-content-end gap-3">
                    <div class="text-center">
                        <div class="text-muted small">Periode</div>
                        <div class="fw-bold">
                            {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Transaksi</div>
                        <div class="fs-3 fw-bold">{{ $totalTransaksi }}</div>
                    </div>
                    <i class="bi bi-receipt fs-1 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Pendapatan</div>
                        <div class="fs-3 fw-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                    </div>
                    <i class="bi bi-cash-coin fs-1 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Rata-rata/Transaksi</div>
                        <div class="fs-3 fw-bold">Rp {{ number_format($rataTransaksi, 0, ',', '.') }}</div>
                    </div>
                    <i class="bi bi-graph-up fs-1 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Metode Pembayaran</div>
                        <div class="fs-3 fw-bold">{{ $tunai + $qris }}</div>
                        <div class="small">
                            Tunai: {{ $tunai }} | QRIS: {{ $qris }}
                        </div>
                    </div>
                    <i class="bi bi-credit-card fs-1 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Pengeluaran</div>
                        <div class="fs-3 fw-bold">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                    </div>
                    <i class="bi bi-arrow-up-right fs-1 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Laba Bersih</div>
                        <div class="fs-3 fw-bold">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
                    </div>
                    <i class="bi bi-calculator fs-1 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card bg-light h-100">
            <div class="card-body">
                <h6><i class="bi bi-trophy"></i> Produk Terlaris</h6>
                @if($topProducts->count() > 0)
                <ol class="mb-0">
                    @foreach($topProducts as $product)
                    <li>
                        {{ $product->produk->nama_produk ?? 'Produk tidak ditemukan' }}
                        <span class="badge bg-primary float-end">{{ $product->total_qty }} pcs</span>
                    </li>
                    @endforeach
                </ol>
                @else
                <p class="text-muted mb-0">Belum ada data penjualan</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tabs for Transactions and Pengeluaran -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="laporanTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="transaksi-tab" data-bs-toggle="tab" 
                        data-bs-target="#transaksi" type="button" role="tab">
                    <i class="bi bi-cart-check"></i> Transaksi Penjualan ({{ $transactions->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pengeluaran-tab" data-bs-toggle="tab" 
                        data-bs-target="#pengeluaran" type="button" role="tab">
                    <i class="bi bi-cash-stack"></i> Pengeluaran ({{ $pengeluaran->count() }})
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="laporanTabsContent">
            <!-- TAB TRANSAKSI -->
            <div class="tab-pane fade show active" id="transaksi" role="tabpanel">
                @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th width="120">No. Transaksi</th>
                                <th>Nama Pembeli</th>
                                <th width="100">Kasir</th>
                                <th width="100">Metode</th>
                                <th width="120">Total</th>
                                <th width="150">Tanggal</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $trans)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        TRX-{{ str_pad($trans->id_transaksi, 6, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td>{{ $trans->nama_pembeli }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $trans->user->nama_user ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($trans->metode_pembayaran == 'tunai')
                                    <span class="badge bg-success">Tunai</span>
                                    @else
                                    <span class="badge bg-primary">QRIS</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-success">
                                    Rp {{ number_format($trans->total_harga, 0, ',', '.') }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($trans->tanggal)->translatedFormat('d M Y H:i') }}
                                </td>
                                <td>
                                    <a href="{{ route('riwayat.show', $trans->id_transaksi) }}" 
                                       class="btn btn-sm btn-info" title="Detail" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('transaksi.print', $trans->id_transaksi) }}" 
                                       class="btn btn-sm btn-secondary" title="Cetak Struk" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">
                                    Total Pendapatan:
                                </td>
                                <td class="fw-bold text-success fs-5">
                                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-3">Tidak ada transaksi pada periode ini</p>
                </div>
                @endif
            </div>
            
            <!-- TAB PENGELUARAN -->
            <div class="tab-pane fade" id="pengeluaran" role="tabpanel">
                @if($pengeluaran->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Nama Pengeluaran</th>
                                <th width="120">Nominal</th>
                                <th width="120">Tanggal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengeluaran as $peng)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $peng->nama_pengeluaran }}</td>
                                <td class="fw-bold text-danger">
                                    Rp {{ number_format($peng->nominal, 0, ',', '.') }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($peng->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    @if($peng->keterangan)
                                    <span class="text-muted small">{{ $peng->keterangan }}</span>
                                    @else
                                    <span class="text-muted fst-italic">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end fw-bold">
                                    Total Pengeluaran:
                                </td>
                                <td class="fw-bold text-danger fs-5">
                                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-cash-coin" style="font-size: 3rem;"></i>
                    <p class="mt-3">Tidak ada pengeluaran pada periode ini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Summary Card -->
<div class="card mt-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">
            <i class="bi bi-clipboard-data"></i> Ringkasan Laporan
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <h6>Pendapatan vs Pengeluaran</h6>
                <div class="progress mb-2" style="height: 30px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $totalPendapatan > 0 ? 100 : 0 }}%">
                        Pendapatan: Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </div>
                </div>
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-danger" role="progressbar" 
                         style="width: {{ $totalPendapatan > 0 ? ($totalPengeluaran / $totalPendapatan * 100) : 0 }}%">
                        Pengeluaran: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <h6>Metode Pembayaran</h6>
                <div class="d-flex justify-content-between mb-1">
                    <span>Tunai</span>
                    <span>{{ $tunai }} transaksi</span>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ ($tunai + $qris) > 0 ? ($tunai / ($tunai + $qris) * 100) : 0 }}%">
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>QRIS</span>
                    <span>{{ $qris }} transaksi</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-primary" role="progressbar" 
                         style="width: {{ ($tunai + $qris) > 0 ? ($qris / ($tunai + $qris) * 100) : 0 }}%">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <h6>Laba Bersih</h6>
                <div class="text-center">
                    <div class="display-4 fw-bold 
                        {{ $labaBersih > 0 ? 'text-success' : ($labaBersih < 0 ? 'text-danger' : 'text-secondary') }}">
                        Rp {{ number_format($labaBersih, 0, ',', '.') }}
                    </div>
                    <p class="text-muted">
                        @if($labaBersih > 0)
                        <i class="bi bi-arrow-up-circle text-success"></i> Laba
                        @elseif($labaBersih < 0)
                        <i class="bi bi-arrow-down-circle text-danger"></i> Rugi
                        @else
                        <i class="bi bi-dash-circle text-secondary"></i> Impas
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Simple scripts untuk tabs
    $(document).ready(function() {
        // Aktifkan tab berdasarkan URL hash
        const hash = window.location.hash;
        if (hash) {
            const tab = new bootstrap.Tab($(`[href="${hash}"]`));
            tab.show();
        }
        
        // Update URL hash saat tab berubah
        $('#laporanTabs button').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });
    });
    
    // Print laporan
    function printLaporan() {
        window.open(`{{ route('pemilik.laporan.print') }}?start_date={{ $startDate }}&end_date={{ $endDate }}`, '_blank');
    }
</script>
@endsection