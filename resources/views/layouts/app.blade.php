<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Warkop Mugi Berkah')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        
        /* Custom Colors */
        .bg-warkop { background-color: #8b2323; }
        .text-warkop { color: #8b2323; }
        .hover-text-warkop:hover { color: #8b2323; }
        .border-warkop { border-color: #8b2323; }
        
        /* Smooth Sidebar Transition */
        #sidebar-drawer { transition: transform 0.3s ease-in-out; }
        .sidebar-open { transform: translateX(0) !important; }
        .sidebar-closed { transform: translateX(-100%); }
    </style>
    
    @stack('styles')
</head>
<body class="text-gray-800 antialiased bg-gray-50">

    <nav class="fixed top-0 left-0 right-0 bg-white z-40 border-b border-gray-200 h-16 px-4 flex items-center justify-between shadow-sm">
        
        <button id="hamburger-btn" class="text-gray-600 hover:text-warkop focus:outline-none p-2 transition-colors rounded-lg hover:bg-gray-100">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>

        <h1 class="text-lg font-bold text-gray-800 tracking-tight absolute left-1/2 -translate-x-1/2 w-max">
            @yield('header-title', 'Aplikasi Warkop')
        </h1>

        <div class="relative">
            <button id="settings-btn" class="text-gray-600 hover:text-warkop focus:outline-none p-2 transition-colors rounded-lg hover:bg-gray-100">
                <i class="fa-solid fa-gear text-xl"></i>
            </button>

            <div id="settings-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-50 origin-top-right ring-1 ring-black ring-opacity-5">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <p class="text-sm font-bold text-gray-900">Halo, {{ auth()->user()->nama_user ?? 'User' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ ucfirst(auth()->user()->role ?? '') }}</p>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 font-medium">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div id="sidebar-backdrop" class="fixed inset-0 bg-gray-900/50 z-40 hidden transition-opacity backdrop-blur-sm"></div>

    <aside id="sidebar-drawer" class="fixed top-0 left-0 bottom-0 w-72 bg-white z-50 sidebar-closed shadow-2xl flex flex-col h-full border-r border-gray-200">
        
        <div class="h-16 flex items-center justify-between px-6 bg-white">
            <div class="font-bold text-xl flex items-center gap-2">
                <i class="fa-solid fa-mug-hot"></i> <span>Warkop App</span>
            </div>
            <button id="close-sidebar-btn" class="text-white/80 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            
            @auth
                @if(auth()->user()->role == 'kasir')
                    <div class="px-2 mb-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Menu Kasir</div>
                    
                    <a href="{{ route('transaksi.create') }}" 
                       class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 font-medium
                       {{ request()->routeIs('transaksi.*') ? 'bg-warkop text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-warkop' }}">
                        <i class="fa-solid fa-cash-register w-5 text-center"></i>
                        <span>Transaksi</span>
                    </a>

                    <a href="{{ route('produk.index') }}" 
                       class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 font-medium
                       {{ request()->routeIs('produk.*') ? 'bg-warkop text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-warkop' }}">
                        <i class="fa-solid fa-box w-5 text-center"></i>
                        <span>Kelola Produk</span>
                    </a>

                    <a href="{{ route('kategori.index') }}" 
                       class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 font-medium
                       {{ request()->routeIs('kategori.*') ? 'bg-warkop text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-warkop' }}">
                        <i class="fa-solid fa-tags w-5 text-center"></i>
                        <span>Kategori</span>
                    </a>

                    <a href="{{ route('pengeluaran.index') }}" 
                       class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 font-medium
                       {{ request()->routeIs('pengeluaran.*') ? 'bg-warkop text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-warkop' }}">
                        <i class="fa-solid fa-wallet w-5 text-center"></i>
                        <span>Pengeluaran</span>
                    </a>
                @endif

                @if(auth()->user()->role == 'admin')
                    <div class="px-2 mb-3 mt-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Menu Pemilik</div>
                    
                    <a href="{{ route('pemilik.pegawai.index') }}" 
                       class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 font-medium
                       {{ request()->routeIs('pemilik.pegawai.*') ? 'bg-warkop text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-warkop' }}">
                        <i class="fa-solid fa-users w-5 text-center"></i>
                        <span>Kelola Pegawai</span>
                    </a>

                    <a href="{{ route('pemilik.laporan.index') }}" 
                       class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 font-medium
                       {{ request()->routeIs('pemilik.laporan.*') ? 'bg-warkop text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-warkop' }}">
                        <i class="fa-solid fa-chart-line w-5 text-center"></i>
                        <span>Laporan Keuangan</span>
                    </a>
                @endif
            @endauth

        </div>

        <div class="p-4 border-t border-gray-100 bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold shadow-sm">
                    {{ strtoupper(substr(auth()->user()->nama_user ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">{{ auth()->user()->nama_user ?? 'Guest' }}</p>
                    <p class="text-xs text-gray-500 font-medium">{{ ucfirst(auth()->user()->role ?? '-') }}</p>
                </div>
            </div>
        </div>
    </aside>

    <main class="pt-20 pb-24 px-4 w-full max-w-lg mx-auto min-h-screen">
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // --- LOGIC SIDEBAR ---
            const sidebar = $('#sidebar-drawer');
            const backdrop = $('#sidebar-backdrop');

            function openSidebar() {
                sidebar.removeClass('sidebar-closed').addClass('sidebar-open');
                backdrop.removeClass('hidden').hide().fadeIn(200);
                $('body').addClass('overflow-hidden');
            }

            function closeSidebar() {
                sidebar.removeClass('sidebar-open').addClass('sidebar-closed');
                backdrop.fadeOut(200, function() {
                    $(this).addClass('hidden');
                });
                $('body').removeClass('overflow-hidden');
            }

            $('#hamburger-btn').click(function(e) {
                e.stopPropagation();
                openSidebar();
            });

            $('#close-sidebar-btn, #sidebar-backdrop').click(function() {
                closeSidebar();
            });

            // --- LOGIC SETTINGS DROPDOWN ---
            const settingsBtn = $('#settings-btn');
            const settingsDropdown = $('#settings-dropdown');

            settingsBtn.click(function(e) {
                e.stopPropagation();
                settingsDropdown.toggleClass('hidden');
            });

            $(document).click(function(e) {
                if (!$(e.target).closest('#settings-btn, #settingsDropdown').length) {
                    settingsDropdown.addClass('hidden');
                }
            });
        });
    </script>
    
    @stack('scripts')
    @yield('scripts')
</body>
</html>