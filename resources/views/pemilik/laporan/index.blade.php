@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('styles')
<style>
    .card-stat {
        border-radius: 10px;
        transition: transform 0.3s;
    }
    .card-stat:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    .stat-value {
        font-size: 1.8rem;
        font-weight: bold;
    }
    .date-range {
        max-width: 300px;
    }
    .chart-container {
        height: 300px;
        position: relative;
    }
    .table-transaksi {
        font-size: 0.9rem;
    }
    .total-row {
        background-color: #f8f9fa;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-bar-chart"></i> Laporan Penjualan
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-outline-primary" onclick="filterByPeriod('today')">
                Hari Ini
            </button>
            <button type="button" class="btn btn-outline-primary" onclick="filterByPeriod('week')">
                Minggu Ini
            </button>
            <button type="button" class="btn btn-outline-primary" onclick="filterByPeriod('month')">
                Bulan Ini
            </button>
        </div>
        <button type="button" class="btn btn-success" onclick="printLaporan()">
            <i class="bi bi-printer"></i> Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-range"></i>
                    </span>
                    <input type="date" class="form-control" id="startDate" 
                           value="{{ date('Y-m-01') }}">
                    <span class="input-group-text">s/d</span>
                    <input type="date" class="form-control" id="endDate" 
                           value="{{ date('Y-m-d') }}">
                    <button class="btn btn-primary" type="button" onclick="filterByDate()">
                        Filter
                    </button>
                </div>
            </div>
            <div class="col-md-8 text-end">
                <div class="d-flex justify-content-end gap-3">
                    <div class="text-center">
                        <div class="text-muted small">Periode</div>
                        <div class="fw-bold" id="periodLabel">
                            {{ date('d M Y') }} - {{ date('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card card-stat text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Transaksi</div>
                        <div class="stat-value" id="totalTransaksi">0</div>
                    </div>
                    <i class="bi bi-receipt stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card card-stat text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Pendapatan</div>
                        <div class="stat-value" id="totalPendapatan">Rp 0</div>
                    </div>
                    <i class="bi bi-cash-coin stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card card-stat text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Rata-rata/Transaksi</div>
                        <div class="stat-value" id="rataTransaksi">Rp 0</div>
                    </div>
                    <i class="bi bi-graph-up stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card card-stat text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Metode Pembayaran</div>
                        <div class="stat-value" id="totalMetode">-</div>
                    </div>
                    <i class="bi bi-credit-card stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-list-check"></i> Daftar Transaksi
        </h5>
        <div>
            <button class="btn btn-sm btn-outline-secondary" onclick="exportToExcel()">
                <i class="bi bi-file-excel"></i> Export Excel
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-transaksi" id="transactionsTable">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="120">No. Transaksi</th>
                        <th>Nama Pembeli</th>
                        <th width="120">Kasir</th>
                        <th width="100">Metode</th>
                        <th width="120">Total</th>
                        <th width="150">Tanggal</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody id="transactionsBody">
                    <!-- Data akan diisi via JavaScript -->
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat data...</p>
                        </td>
                    </tr>
                </tbody>
                <tfoot id="transactionsFooter">
                    <!-- Total akan diisi via JavaScript -->
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="detailTransaksiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-receipt"></i> Detail Transaksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailTransaksiContent">
                <!-- Content akan diisi via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printStruk()">
                    <i class="bi bi-printer"></i> Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentTransaksi = null;
    
    $(document).ready(function() {
        // Load initial data
        loadLaporanData();
        
        // Set default date range
        updatePeriodLabel();
        
        // Setup date inputs
        $('#startDate, #endDate').change(updatePeriodLabel);
    });
    
    // Load laporan data
    function loadLaporanData() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        
        $.ajax({
            url: '{{ route("pemilik.laporan.data") }}',
            method: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                if (response.success) {
                    updateStats(response.stats);
                    updateTransactionsTable(response.transactions);
                } else {
                    showToast('Error', 'Gagal memuat data laporan', 'error');
                }
            },
            error: function() {
                showToast('Error', 'Terjadi kesalahan', 'error');
            }
        });
    }
    
    // Update statistics
    function updateStats(stats) {
        $('#totalTransaksi').text(stats.total_transaksi);
        $('#totalPendapatan').text('Rp ' + stats.total_pendapatan.toLocaleString('id-ID'));
        $('#rataTransaksi').text('Rp ' + stats.rata_transaksi.toLocaleString('id-ID'));
        
        // Metode pembayaran
        let metodeText = '';
        if (stats.tunai > 0) metodeText += `Tunai: ${stats.tunai}<br>`;
        if (stats.qris > 0) metodeText += `QRIS: ${stats.qris}`;
        $('#totalMetode').html(metodeText || '-');
    }
    
    // Update transactions table
    function updateTransactionsTable(transactions) {
        const tbody = $('#transactionsBody');
        const tfoot = $('#transactionsFooter');
        
        if (transactions.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                        <p class="mt-3">Tidak ada transaksi pada periode ini</p>
                    </td>
                </tr>
            `);
            tfoot.html('');
            return;
        }
        
        let html = '';
        let totalPendapatan = 0;
        
        transactions.forEach((trans, index) => {
            totalPendapatan += trans.total_harga;
            
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <span class="badge bg-light text-dark">
                            TRX-${trans.id_transaksi.toString().padStart(4, '0')}
                        </span>
                    </td>
                    <td>${trans.nama_pembeli}</td>
                    <td>
                        <span class="badge bg-info">${trans.user.nama_user}</span>
                    </td>
                    <td>
                        ${trans.metode_pembayaran === 'tunai' ? 
                          '<span class="badge bg-success">Tunai</span>' : 
                          '<span class="badge bg-primary">QRIS</span>'}
                    </td>
                    <td class="fw-bold text-success">
                        Rp ${trans.total_harga.toLocaleString('id-ID')}
                    </td>
                    <td>
                        ${new Date(trans.tanggal).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" 
                                onclick="showDetailTransaksi(${trans.id_transaksi})"
                                title="Detail">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-secondary" 
                                onclick="printSingleStruk(${trans.id_transaksi})"
                                title="Cetak">
                            <i class="bi bi-printer"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tbody.html(html);
        
        // Footer dengan total
        tfoot.html(`
            <tr class="total-row">
                <td colspan="5" class="text-end">
                    <strong>Total Pendapatan:</strong>
                </td>
                <td class="fw-bold text-success">
                    Rp ${totalPendapatan.toLocaleString('id-ID')}
                </td>
                <td colspan="2"></td>
            </tr>
        `);
    }
    
    // Filter by period
    function filterByPeriod(period) {
        const today = new Date();
        let startDate, endDate;
        
        switch(period) {
            case 'today':
                startDate = endDate = today.toISOString().split('T')[0];
                break;
            case 'week':
                startDate = new Date(today.setDate(today.getDate() - today.getDay()));
                endDate = new Date();
                startDate = startDate.toISOString().split('T')[0];
                endDate = endDate.toISOString().split('T')[0];
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                startDate = startDate.toISOString().split('T')[0];
                endDate = endDate.toISOString().split('T')[0];
                break;
        }
        
        $('#startDate').val(startDate);
        $('#endDate').val(endDate);
        updatePeriodLabel();
        loadLaporanData();
    }
    
    // Filter by custom date
    function filterByDate() {
        loadLaporanData();
    }
    
    // Update period label
    function updatePeriodLabel() {
        const start = $('#startDate').val();
        const end = $('#endDate').val();
        
        if (start && end) {
            const startDate = new Date(start);
            const endDate = new Date(end);
            
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            const startStr = startDate.toLocaleDateString('id-ID', options);
            const endStr = endDate.toLocaleDateString('id-ID', options);
            
            $('#periodLabel').text(`${startStr} - ${endStr}`);
        }
    }
    
    // Show transaction detail
    function showDetailTransaksi(id) {
        $.ajax({
            url: '/riwayat/' + id,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    currentTransaksi = response.data;
                    showDetailModal(response.data);
                } else {
                    showToast('Error', 'Gagal memuat detail transaksi', 'error');
                }
            },
            error: function() {
                showToast('Error', 'Terjadi kesalahan', 'error');
            }
        });
    }
    
    // Show detail modal
    function showDetailModal(transaksi) {
        let itemsHtml = '';
        
        transaksi.detail_transaksi.forEach(item => {
            itemsHtml += `
                <tr>
                    <td>${item.produk.nama_produk}</td>
                    <td class="text-center">${item.qty}</td>
                    <td class="text-end">Rp ${item.harga_satuan.toLocaleString('id-ID')}</td>
                    <td class="text-end">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                </tr>
            `;
        });
        
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Informasi Transaksi</h6>
                    <table class="table table-sm">
                        <tr>
                            <td width="120">No. Transaksi</td>
                            <td><strong>TRX-${transaksi.id_transaksi.toString().padStart(4, '0')}</strong></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>${new Date(transaksi.tanggal).toLocaleString('id-ID')}</td>
                        </tr>
                        <tr>
                            <td>Nama Pembeli</td>
                            <td>${transaksi.nama_pembeli}</td>
                        </tr>
                        <tr>
                            <td>Kasir</td>
                            <td>${transaksi.user.nama_user}</td>
                        </tr>
                        <tr>
                            <td>Metode Bayar</td>
                            <td>
                                ${transaksi.metode_pembayaran === 'tunai' ? 
                                  '<span class="badge bg-success">Tunai</span>' : 
                                  '<span class="badge bg-primary">QRIS</span>'}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Pembayaran</h6>
                    <table class="table table-sm">
                        ${transaksi.metode_pembayaran === 'tunai' ? `
                        <tr>
                            <td>Uang Diterima</td>
                            <td class="text-end">Rp ${transaksi.uang_diterima?.toLocaleString('id-ID') || '0'}</td>
                        </tr>
                        <tr>
                            <td>Kembalian</td>
                            <td class="text-end text-success">Rp ${transaksi.uang_kembalian?.toLocaleString('id-ID') || '0'}</td>
                        </tr>
                        ` : ''}
                        <tr>
                            <td><strong>Total</strong></td>
                            <td class="text-end">
                                <h5 class="text-success mb-0">Rp ${transaksi.total_harga.toLocaleString('id-ID')}</h5>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <hr>
            
            <h6>Detail Pesanan</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                    </tbody>
                </table>
            </div>
        `;
        
        $('#detailTransaksiContent').html(content);
        $('#detailTransaksiModal').modal('show');
    }
    
    // Print laporan
    function printLaporan() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        
        window.open(`/pemilik/laporan/print?start_date=${startDate}&end_date=${endDate}`, '_blank');
    }
    
    // Print single struk
    function printSingleStruk(id) {
        window.open(`/transaksi/${id}/print`, '_blank');
    }
    
    // Export to Excel
    function exportToExcel() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        
        window.open(`/pemilik/laporan/export?start_date=${startDate}&end_date=${endDate}`, '_blank');
    }
    
    // Helper function untuk toast/alert
    function showToast(title, message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 'alert-info';
        
        const icon = type === 'success' ? 'bi-check-circle' :
                    type === 'error' ? 'bi-exclamation-triangle' : 'bi-info-circle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bi ${icon}"></i>
                <strong>${title}</strong><br>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.alert-fixed').remove();
        $('body').append(alertHtml);
        
        setTimeout(() => {
            $('.alert-fixed').alert('close');
        }, 5000);
    }
</script>
@endsection