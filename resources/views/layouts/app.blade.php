<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Warkop Mugi Berkah')</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        body { padding-top: 60px; background-color: #f8f9fa; }
        .sidebar { position: fixed; top: 60px; bottom: 0; left: 0; z-index: 100; padding: 20px 0; overflow-x: hidden; overflow-y: auto; background-color: #fff; border-right: 1px solid #dee2e6; }
        .sidebar .nav-link { font-weight: 500; color: #333; padding: 10px 20px; }
        .sidebar .nav-link.active { color: #0d6efd; background-color: #e7f1ff; }
        .sidebar .nav-link:hover { color: #0d6efd; }
        .navbar-brand { font-weight: bold; }
        .card { border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .table th { border-top: none; }
        .btn-action { width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-cup-hot"></i> Warkop Mugi Berkah
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->nama_user }}
                            <span class="badge bg-light text-dark ms-1">{{ auth()->user()->role }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (hanya untuk yang sudah login) -->
            @auth
            <div class="col-md-2 d-none d-md-block sidebar">
                <nav class="nav flex-column">
                    @if(auth()->user()->isKasir())
                        <!-- Menu Kasir -->
                        <a class="nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}" href="{{ route('transaksi.create') }}">
                            <i class="bi bi-cart-plus"></i> Transaksi Baru
                        </a>
                        <a class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" href="{{ route('produk.index') }}">
                            <i class="bi bi-box"></i> Kelola Produk
                        </a>
                        <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
                            <i class="bi bi-tags"></i> Kelola Kategori
                        </a>
                        <a class="nav-link {{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}" href="{{ route('pengeluaran.index') }}">
                            <i class="bi bi-cash-stack"></i> Pengeluaran
                        </a>
                    @endif
                    
                    @if(auth()->user()->isAdmin())
                        <!-- Menu Admin -->
                        <a class="nav-link {{ request()->routeIs('pemilik.pegawai.*') ? 'active' : '' }}" href="{{ route('pemilik.pegawai.index') }}">
                            <i class="bi bi-people"></i> Kelola Pegawai
                        </a>
                        <a class="nav-link {{ request()->routeIs('pemilik.laporan.*') ? 'active' : '' }}" href="{{ route('pemilik.laporan.index') }}">
                            <i class="bi bi-bar-chart"></i> Laporan
                        </a>
                    @endif
                </nav>
            </div>
            @endauth
            
            <!-- Main Content Area -->
            <main class="@auth col-md-10 @else col-12 @endauth ms-sm-auto px-4">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (optional, untuk AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>