@extends('layouts.app')

@section('title', 'Laporan')
@section('header-title', 'Laporan Keuangan')

@section('content')

<div class="mb-4">
    <div class="flex overflow-x-auto pb-2 gap-2 no-scrollbar">
        <a href="{{ route('pemilik.laporan.index', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" 
           class="whitespace-nowrap px-3 py-1.5 text-xs font-medium rounded-full border {{ request('start_date') == date('Y-m-d') ? 'bg-[#a52a2a] text-white border-[#a52a2a]' : 'bg-white text-gray-600 border-gray-200' }}">
            Hari Ini
        </a>
        
        @php
            $startOfWeek = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');
            $endOfWeek = \Carbon\Carbon::now()->endOfWeek()->format('Y-m-d');
        @endphp
        <a href="{{ route('pemilik.laporan.index', ['start_date' => $startOfWeek, 'end_date' => $endOfWeek]) }}" 
           class="whitespace-nowrap px-3 py-1.5 text-xs font-medium rounded-full border {{ request('start_date') == $startOfWeek ? 'bg-[#a52a2a] text-white border-[#a52a2a]' : 'bg-white text-gray-600 border-gray-200' }}">
            Minggu Ini
        </a>

        @php
            $startOfMonth = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
            $endOfMonth = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
        @endphp
        <a href="{{ route('pemilik.laporan.index', ['start_date' => $startOfMonth, 'end_date' => $endOfMonth]) }}" 
           class="whitespace-nowrap px-3 py-1.5 text-xs font-medium rounded-full border {{ request('start_date') == $startOfMonth ? 'bg-[#a52a2a] text-white border-[#a52a2a]' : 'bg-white text-gray-600 border-gray-200' }}">
            Bulan Ini
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-4 overflow-hidden">
    <button onclick="document.getElementById('filterForm').classList.toggle('hidden')" 
            class="w-full flex justify-between items-center px-4 py-3 bg-gray-50 text-xs font-bold text-gray-600 uppercase tracking-wide">
        <span><i class="fa-regular fa-calendar mr-2"></i> {{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
        <i class="fa-solid fa-chevron-down text-gray-400"></i>
    </button>
    
    <div id="filterForm" class="hidden p-4 border-t border-gray-100">
        <form method="GET" action="{{ route('pemilik.laporan.index') }}" class="flex flex-col gap-3">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-[10px] text-gray-400 font-bold uppercase">Dari</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-sm border-gray-200 rounded bg-gray-50 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-[10px] text-gray-400 font-bold uppercase">Sampai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-sm border-gray-200 rounded bg-gray-50 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-[#a52a2a] text-white py-2 rounded text-sm font-bold">Terapkan</button>
                <a href="{{ route('pemilik.laporan.print', request()->all()) }}" target="_blank" class="px-4 py-2 bg-green-100 text-green-700 rounded text-sm font-bold">
                    <i class="fa-solid fa-print"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-2 gap-3 mb-4">
    <div class="bg-white p-3 rounded-xl border border-blue-100 shadow-sm relative overflow-hidden">
        <p class="text-[10px] text-gray-400 uppercase font-bold">Pendapatan</p>
        <p class="text-lg font-bold text-blue-600 truncate">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        <div class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
            <i class="fa-solid fa-receipt"></i> {{ $totalTransaksi }} Trx
        </div>
    </div>

    <div class="bg-white p-3 rounded-xl border border-red-100 shadow-sm relative overflow-hidden">
        <p class="text-[10px] text-gray-400 uppercase font-bold">Pengeluaran</p>
        <p class="text-lg font-bold text-red-500 truncate">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
        <div class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
            <i class="fa-solid fa-arrow-trend-down"></i> {{ $pengeluaran->count() }} Item
        </div>
    </div>

    <div class="col-span-2 bg-gray-800 text-white p-4 rounded-xl shadow-md flex justify-between items-center">
        <div>
            <p class="text-[10px] text-gray-400 uppercase font-bold">Laba Bersih</p>
            <p class="text-xl font-bold {{ $labaBersih < 0 ? 'text-red-400' : 'text-green-400' }}">
                Rp {{ number_format($labaBersih, 0, ',', '.') }}
            </p>
        </div>
        <div class="text-right">
            <div class="text-[10px] text-gray-400">Margin</div>
            <div class="font-bold text-sm">
                @if($totalPendapatan > 0)
                    {{ round(($labaBersih / $totalPendapatan) * 100, 1) }}%
                @else 0% @endif
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-100 p-1 rounded-lg flex mb-3">
    <button onclick="switchTab('transaksi')" id="tab-btn-transaksi" class="flex-1 py-1.5 text-xs font-bold rounded-md shadow bg-white text-gray-800 transition-all">
        Transaksi
    </button>
    <button onclick="switchTab('pengeluaran')" id="tab-btn-pengeluaran" class="flex-1 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-700 transition-all">
        Pengeluaran
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden min-h-[300px]">
    
    <div id="tab-content-transaksi">
        @if($transactions->count() > 0)
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3">Info</th>
                            <th class="px-4 py-3">Metode</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transactions as $trans)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-bold text-sm text-gray-800">{{ $trans->nama_pembeli }}</div>
                                <div class="text-xs text-gray-400">#{{ $trans->id_transaksi }} • {{ $trans->user->nama_user ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $trans->metode_pembayaran == 'tunai' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ strtoupper($trans->metode_pembayaran) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="font-bold text-sm text-gray-800">Rp {{ number_format($trans->total_harga, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($trans->tanggal)->format('d/m H:i') }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="md:hidden divide-y divide-gray-100">
                @foreach($transactions as $trans)
                <div class="p-4 flex justify-between items-start active:bg-gray-50">
                    <div>
                        <div class="font-bold text-gray-800 text-sm mb-0.5">{{ $trans->nama_pembeli }}</div>
                        <div class="text-[10px] text-gray-400 flex items-center gap-1">
                            <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($trans->tanggal)->format('d M, H:i') }}
                            <span class="text-gray-300">|</span>
                            {{ $trans->user->nama_user ?? 'Staff' }}
                        </div>
                        <div class="mt-2">
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $trans->metode_pembayaran == 'tunai' ? 'border-green-200 text-green-700 bg-green-50' : 'border-blue-200 text-blue-700 bg-blue-50' }}">
                                {{ strtoupper($trans->metode_pembayaran) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-sm text-gray-900">Rp {{ number_format($trans->total_harga, 0, ',', '.') }}</div>
                        <div class="text-[10px] text-gray-400 mt-1">#{{ substr($trans->id_transaksi, -4) }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10">
                <i class="fa-solid fa-inbox text-3xl text-gray-200 mb-2"></i>
                <p class="text-sm text-gray-400">Belum ada transaksi</p>
            </div>
        @endif
    </div>

    <div id="tab-content-pengeluaran" class="hidden">
        @if($pengeluaran->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($pengeluaran as $peng)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <div class="font-bold text-gray-800 text-sm">{{ $peng->nama_pengeluaran }}</div>
                        <div class="text-[10px] text-gray-500 mt-0.5 line-clamp-1">{{ $peng->keterangan ?? '-' }}</div>
                        <div class="text-[10px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($peng->tanggal)->format('d M Y') }}</div>
                    </div>
                    <div class="text-right font-bold text-sm text-red-500">
                        - Rp {{ number_format($peng->nominal, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10">
                <i class="fa-solid fa-file-invoice text-3xl text-gray-200 mb-2"></i>
                <p class="text-sm text-gray-400">Belum ada pengeluaran</p>
            </div>
        @endif
    </div>
</div>

<div class="mt-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <h3 class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider">Top Produk</h3>
    @foreach($topProducts->take(3) as $index => $product)
    <div class="flex items-center justify-between mb-3 last:mb-0">
        <div class="flex items-center gap-3">
            <div class="w-6 h-6 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-xs font-bold border border-yellow-100">
                {{ $index + 1 }}
            </div>
            <div class="text-sm font-medium text-gray-700 truncate w-32 md:w-auto">
                {{ $product->produk->nama_produk ?? 'Hapus' }}
            </div>
        </div>
        <div class="text-xs font-bold text-gray-900">{{ $product->total_qty }} pcs</div>
    </div>
    @endforeach
</div>

@endsection

@section('scripts')
<script>
    function switchTab(tabName) {
        // Hide All
        document.getElementById('tab-content-transaksi').classList.add('hidden');
        document.getElementById('tab-content-pengeluaran').classList.add('hidden');
        
        // Show Active
        document.getElementById('tab-content-' + tabName).classList.remove('hidden');

        // Reset Buttons Styles
        const btnTrans = document.getElementById('tab-btn-transaksi');
        const btnPeng = document.getElementById('tab-btn-pengeluaran');
        
        const activeClass = ['bg-white', 'text-gray-800', 'shadow'];
        const inactiveClass = ['text-gray-500', 'hover:text-gray-700', 'bg-transparent', 'shadow-none'];

        // Helper to reset
        function setInactive(btn) {
            btn.classList.remove(...activeClass);
            btn.classList.add(...inactiveClass);
        }
        function setActive(btn) {
            btn.classList.remove(...inactiveClass);
            btn.classList.add(...activeClass);
        }

        if(tabName === 'transaksi') {
            setActive(btnTrans);
            setInactive(btnPeng);
        } else {
            setInactive(btnTrans);
            setActive(btnPeng);
        }
    }
</script>
@endsection