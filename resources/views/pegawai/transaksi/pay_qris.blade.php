@extends('layouts.app')

@section('title', 'Pembayaran QRIS')
@section('header-title', 'Pembayaran')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center p-4">
    
    <div class="bg-white w-full rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-center p-8">
        
        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-qrcode text-3xl"></i>
        </div>

        <h2 class="text-xl font-bold text-gray-800 mb-2">Scan QRIS</h2>
        <p class="text-gray-500 text-sm mb-6">Silahkan minta pelanggan scan QR Code yang muncul setelah tombol ditekan.</p>

        <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-100">
            <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Total Tagihan</span>
            <div class="text-2xl font-bold text-gray-900 mt-1">
                Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
            </div>
            <div class="mt-2 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold">
                <i class="fa-solid fa-clock"></i> {{ strtoupper($transaksi->status) }}
            </div>
        </div>
        
        <button id="pay-button" class="w-full bg-warkop text-white font-bold py-3.5 rounded-xl shadow-lg hover:bg-red-800 transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-expand"></i>
            Tampilkan QRIS / Bayar
        </button>

        <a href="{{ route('transaksi.create') }}" class="block mt-4 text-sm text-gray-400 hover:text-gray-600">
            Batalkan Pembayaran
        </a>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script type="text/javascript">
    const payButton = document.getElementById('pay-button');
    const snapToken = '{{ $snapToken }}';

    payButton.addEventListener('click', function (e) {
        e.preventDefault();
        
        if (typeof window.snap === 'undefined') {
            alert("Sistem Pembayaran sedang memuat, coba sesaat lagi.");
            return;
        }

        window.snap.pay(snapToken, {
            onSuccess: function(result){
                window.location.href = "{{ route('transaksi.finish_qris', $transaksi->id_transaksi) }}";
            },
            onPending: function(result){
                alert("Menunggu pembayaran!");
            },
            onError: function(result){
                alert("Pembayaran gagal atau error sistem.");
            },
            onClose: function(){
                // Opsional: Lakukan sesuatu jika popup ditutup
            }
        });
    });
</script>
@endsection