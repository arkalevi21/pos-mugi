@extends('layouts.app')

@section('title', 'Kelola Produk')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-box"></i> Kelola Produk
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
    <!-- FORM TAMBAH/EDIT PRODUK -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header {{ isset($editMode) && $editMode ? 'bg-warning' : 'bg-success' }} text-white">
                <h6 class="mb-0">
                    <i class="bi {{ isset($editMode) && $editMode ? 'bi-pencil' : 'bi-plus-circle' }}"></i> 
                    {{ isset($editMode) && $editMode ? 'Edit Produk' : 'Tambah Produk Baru' }}
                </h6>
            </div>
            <div class="card-body">
                @if(isset($editMode) && $editMode)
                <!-- FORM EDIT PRODUK -->
                <form method="POST" action="{{ route('produk.update', $produkEdit->id_produk) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="edit_nama_produk" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" 
                               id="edit_nama_produk" name="nama_produk" 
                               value="{{ old('nama_produk', $produkEdit->nama_produk) }}" 
                               required maxlength="100"
                               placeholder="Masukkan nama produk">
                        @error('nama_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_id_kategori" class="form-label">Kategori</label>
                                <select class="form-select @error('id_kategori') is-invalid @enderror" 
                                        id="edit_id_kategori" name="id_kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategori as $kat)
                                    <option value="{{ $kat->id_kategori }}" 
                                        {{ old('id_kategori', $produkEdit->id_kategori) == $kat->id_kategori ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('id_kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_harga" class="form-label">Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                                           id="edit_harga" name="harga" 
                                           value="{{ old('harga', $produkEdit->harga) }}" 
                                           required min="0">
                                </div>
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_gambar" class="form-label">Gambar Produk</label>
                        
                        <!-- Preview gambar saat ini -->
                        @if($produkEdit->gambar)
                        <div class="mb-3">
                            <p class="small text-muted mb-1">Gambar saat ini:</p>
                            <img src="{{ asset('storage/products/' . $produkEdit->gambar) }}" 
                                 alt="{{ $produkEdit->nama_produk }}" 
                                 class="img-fluid rounded border" 
                                 style="max-height: 150px;"
                                 onerror="this.style.display='none'">
                        </div>
                        @endif
                        
                        <input type="file" class="form-control @error('gambar') is-invalid @enderror" 
                               id="edit_gambar" name="gambar" accept="image/*">
                        <div class="form-text">
                            Kosongkan jika tidak ingin mengubah gambar. Maksimal 2MB. Format: JPG, PNG, GIF
                        </div>
                        @error('gambar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning flex-grow-1">
                            <i class="bi bi-save"></i> Update Produk
                        </button>
                        <a href="{{ route('produk.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
                @else
                <!-- FORM TAMBAH PRODUK -->
                <form method="POST" action="{{ route('produk.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nama_produk" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" 
                               id="nama_produk" name="nama_produk" 
                               value="{{ old('nama_produk') }}" 
                               required maxlength="100"
                               placeholder="Contoh: Kopi Hitam, Nasi Goreng">
                        @error('nama_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="id_kategori" class="form-label">Kategori</label>
                                <select class="form-select @error('id_kategori') is-invalid @enderror" 
                                        id="id_kategori" name="id_kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategori as $kat)
                                    <option value="{{ $kat->id_kategori }}" 
                                        {{ old('id_kategori') == $kat->id_kategori ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('id_kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                                           id="harga" name="harga" 
                                           value="{{ old('harga') }}" 
                                           required min="0"
                                           placeholder="10000">
                                </div>
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-save"></i> Simpan Produk
                    </button>
                </form>
                @endif
            </div>
        </div>
        
        <!-- INFO -->
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="bi bi-info-circle"></i> Informasi:</h6>
                <ul class="mb-0 small">
                    <li>Produk baru akan otomatis memiliki stok 0</li>
                    <li>Gambar produk opsional</li>
                    <li>Nama produk maksimal 100 karakter</li>
                    <li>Pastikan harga tidak mengandung titik</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- TABEL PRODUK -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-list"></i> Daftar Produk ({{ $produk->count() }})
                    </h6>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($produk->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th width="120">Harga</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produk as $prod)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $prod->nama_produk }}</strong>
                                    @if(isset($editMode) && $editMode && $produkEdit->id_produk == $prod->id_produk)
                                    <span class="badge bg-warning ms-2">Sedang diedit</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $prod->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="fw-bold text-success">
                                    Rp {{ number_format($prod->harga, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('produk.index') }}?edit={{ $prod->id_produk }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('produk.destroy', $prod->id_produk) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Yakin hapus produk {{ $prod->nama_produk }}?')">
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
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-3">Belum ada produk</p>
                    <p class="small">Gunakan form di samping untuk menambahkan produk pertama</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- FILTER SEDERHANA -->
        @if($produk->count() > 0)
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="bi bi-funnel"></i> Filter Kategori:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('produk.index') }}" 
                       class="btn btn-sm {{ !request()->has('kategori') ? 'btn-primary' : 'btn-outline-primary' }}">
                        Semua ({{ $produk->count() }})
                    </a>
                    @foreach($kategori as $kat)
                    @php
                        $countProduk = $produk->where('id_kategori', $kat->id_kategori)->count();
                    @endphp
                    @if($countProduk > 0)
                    <a href="{{ route('produk.index') }}?kategori={{ $kat->id_kategori }}" 
                       class="btn btn-sm {{ request('kategori') == $kat->id_kategori ? 'btn-info' : 'btn-outline-info' }}">
                        {{ $kat->nama_kategori }} ({{ $countProduk }})
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection