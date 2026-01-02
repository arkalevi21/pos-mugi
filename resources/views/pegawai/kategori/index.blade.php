@extends('layouts.app')

@section('title', 'Kelola Kategori')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-tags"></i> Kelola Kategori
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
    <!-- FORM TAMBAH/EDIT -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header {{ isset($editMode) && $editMode ? 'bg-warning' : 'bg-success' }} text-white">
                <h6 class="mb-0">
                    <i class="bi {{ isset($editMode) && $editMode ? 'bi-pencil' : 'bi-plus-circle' }}"></i> 
                    {{ isset($editMode) && $editMode ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                </h6>
            </div>
            <div class="card-body">
                @if(isset($editMode) && $editMode)
                <!-- FORM EDIT -->
                <form method="POST" action="{{ route('kategori.update', $kategoriEdit->id_kategori) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="edit_nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" 
                               id="edit_nama_kategori" name="nama_kategori" 
                               value="{{ old('nama_kategori', $kategoriEdit->nama_kategori) }}" 
                               required maxlength="100"
                               placeholder="Masukkan nama kategori">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning flex-grow-1">
                            <i class="bi bi-save"></i> Update Kategori
                        </button>
                        <a href="{{ route('kategori.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
                @else
                <!-- FORM TAMBAH -->
                <form method="POST" action="{{ route('kategori.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" 
                               id="nama_kategori" name="nama_kategori" 
                               value="{{ old('nama_kategori') }}" 
                               required maxlength="100"
                               placeholder="Contoh: Minuman, Makanan, Snack">
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-save"></i> Simpan Kategori
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
                    <li>Kategori yang sudah digunakan tidak bisa dihapus</li>
                    <li>Nama kategori harus unik</li>
                    <li>Maksimal 100 karakter</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- TABEL KATEGORI -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-list"></i> Daftar Kategori ({{ $kategori->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($kategori->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Nama Kategori</th>
                                <th width="100">Jumlah Produk</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kategori as $kat)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $kat->nama_kategori }}</strong>
                                    @if(isset($editMode) && $editMode && $kategoriEdit->id == $kat->id)
                                    <span class="badge bg-warning ms-2">Sedang diedit</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $kat->produk_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('kategori.index') }}?edit={{ $kat->id_kategori }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('kategori.destroy', $kat->id_kategori) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Yakin hapus kategori {{ $kat->nama_kategori }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus"
                                                    {{ $kat->produk_count > 0 ? 'disabled' : '' }}>
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
                    <p class="mt-3">Belum ada kategori</p>
                    <p class="small">Gunakan form di samping untuk menambahkan kategori pertama</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection