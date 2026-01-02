@extends('layouts.app')

@section('title', 'Pencatatan Pengeluaran')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-cash-stack"></i> Pencatatan Pengeluaran
    </h1>
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

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> Terdapat kesalahan:
    <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <!-- FORM TAMBAH/EDIT PENGELUARAN -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header {{ isset($editMode) && $editMode ? 'bg-warning' : 'bg-success' }} text-white">
                <h6 class="mb-0">
                    <i class="bi {{ isset($editMode) && $editMode ? 'bi-pencil' : 'bi-plus-circle' }}"></i> 
                    {{ isset($editMode) && $editMode ? 'Edit Pengeluaran' : 'Tambah Pengeluaran Baru' }}
                </h6>
            </div>
            <div class="card-body">
                @if(isset($editMode) && $editMode)
                <!-- FORM EDIT PENGELUARAN -->
                <form method="POST" action="{{ route('pengeluaran.update', $pengeluaranEdit->id_pengeluaran) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="edit_nama_pengeluaran" class="form-label">Nama Pengeluaran</label>
                        <input type="text" class="form-control @error('nama_pengeluaran') is-invalid @enderror" 
                               id="edit_nama_pengeluaran" name="nama_pengeluaran" 
                               value="{{ old('nama_pengeluaran', $pengeluaranEdit->nama_pengeluaran) }}" 
                               required maxlength="150"
                               placeholder="Contoh: Beli Kopi, Bayar Listrik">
                        @error('nama_pengeluaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_nominal" class="form-label">Nominal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('nominal') is-invalid @enderror" 
                                           id="edit_nominal" name="nominal" 
                                           value="{{ old('nominal', $pengeluaranEdit->nominal) }}" 
                                           required min="0">
                                </div>
                                @error('nominal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                       id="edit_tanggal" name="tanggal" 
                                       value="{{ old('tanggal', $pengeluaranEdit->tanggal) }}" 
                                       required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                  id="edit_keterangan" name="keterangan" 
                                  rows="3" placeholder="Keterangan tambahan...">{{ old('keterangan', $pengeluaranEdit->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning flex-grow-1">
                            <i class="bi bi-save"></i> Update Pengeluaran
                        </button>
                        <a href="{{ route('pengeluaran.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
                @else
                <!-- FORM TAMBAH PENGELUARAN -->
                <form method="POST" action="{{ route('pengeluaran.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nama_pengeluaran" class="form-label">Nama Pengeluaran</label>
                        <input type="text" class="form-control @error('nama_pengeluaran') is-invalid @enderror" 
                               id="nama_pengeluaran" name="nama_pengeluaran" 
                               value="{{ old('nama_pengeluaran') }}" 
                               required maxlength="150"
                               placeholder="Contoh: Beli Kopi, Bayar Listrik, Beli Gas">
                        @error('nama_pengeluaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nominal" class="form-label">Nominal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('nominal') is-invalid @enderror" 
                                           id="nominal" name="nominal" 
                                           value="{{ old('nominal') }}" 
                                           required min="0"
                                           placeholder="100000">
                                </div>
                                @error('nominal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                       id="tanggal" name="tanggal" 
                                       value="{{ old('tanggal', date('Y-m-d')) }}" 
                                       required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                  id="keterangan" name="keterangan" 
                                  rows="3" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-save"></i> Simpan Pengeluaran
                    </button>
                </form>
                @endif
            </div>
        </div>
        
        <!-- STATISTIK PENGELUARAN -->
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="bi bi-graph-up"></i> Statistik:</h6>
                <div class="mb-2">
                    <span class="small text-muted">Total Pengeluaran:</span>
                    <div class="fw-bold text-danger fs-5">
                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mb-2">
                    <span class="small text-muted">Jumlah Transaksi:</span>
                    <div class="fw-bold">
                        {{ $pengeluaran->count() }} transaksi
                    </div>
                </div>
                @if(request()->has('tanggal'))
                <div class="mt-3">
                    <a href="{{ route('pengeluaran.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-x-circle"></i> Hapus Filter Tanggal
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- TABEL PENGELUARAN -->
    <div class="col-md-8">
        <!-- FILTER TANGGAL -->
        <div class="card mb-3">
            <div class="card-body">
                <h6><i class="bi bi-filter"></i> Filter Tanggal:</h6>
                <form method="GET" action="{{ route('pengeluaran.index') }}" class="row g-2">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-calendar"></i>
                            </span>
                            <input type="date" class="form-control" name="tanggal" 
                                   value="{{ request('tanggal', date('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('pengeluaran.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-list"></i> Daftar Pengeluaran 
                        @if(request()->has('tanggal'))
                        ({{ \Carbon\Carbon::parse(request('tanggal'))->format('d/m/Y') }})
                        @endif
                    </h6>
                    <div>
                        <span class="badge bg-danger">
                            Total: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
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
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengeluaran as $peng)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $peng->nama_pengeluaran }}</strong>
                                    @if(isset($editMode) && $editMode && $pengeluaranEdit->id_pengeluaran == $peng->id_pengeluaran)
                                    <span class="badge bg-warning ms-2">Sedang diedit</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-danger">
                                    Rp {{ number_format($peng->nominal, 0, ',', '.') }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($peng->tanggal)->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($peng->keterangan)
                                    <span class="text-muted small">{{ $peng->keterangan }}</span>
                                    @else
                                    <span class="text-muted fst-italic">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('pengeluaran.index') }}?edit={{ $peng->id_pengeluaran }}@if(request()->has('tanggal'))&tanggal={{ request('tanggal') }}@endif" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('pengeluaran.destroy', $peng->id_pengeluaran) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Yakin hapus pengeluaran {{ $peng->nama_pengeluaran }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- PAGINATION (jika perlu) -->
                @if($pengeluaran->count() > 10)
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
                @endif
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-cash-coin" style="font-size: 3rem;"></i>
                    <p class="mt-3">
                        @if(request()->has('tanggal'))
                        Tidak ada pengeluaran pada tanggal {{ \Carbon\Carbon::parse(request('tanggal'))->format('d/m/Y') }}
                        @else
                        Belum ada pengeluaran
                        @endif
                    </p>
                    <p class="small">Gunakan form di samping untuk mencatat pengeluaran pertama</p>
                </div>
                @endif
            </div>
        </div>
        
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Script sederhana untuk format nominal
    document.addEventListener('DOMContentLoaded', function() {
        // Format input nominal dengan titik
        const nominalInputs = document.querySelectorAll('input[name="nominal"], input[name="edit_nominal"]');
        
        nominalInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = this.value.replace(/[^\d]/g, '');
                if (value) {
                    value = parseInt(value).toLocaleString('id-ID');
                    this.value = value;
                }
            });
            
            input.addEventListener('blur', function(e) {
                let value = this.value.replace(/[^\d]/g, '');
                if (value) {
                    this.value = parseInt(value);
                }
            });
        });
        
        // Set tanggal default untuk form tambah
        const tanggalInput = document.getElementById('tanggal');
        if (tanggalInput && !tanggalInput.value) {
            const today = new Date().toISOString().split('T')[0];
            tanggalInput.value = today;
        }
    });
</script>
@endsection