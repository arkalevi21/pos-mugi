@extends('layouts.app')

@section('title', 'Pembayaran QRIS')

@section('content')
<div class="container py-5">
    <div class="card text-center mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="card-title">Menunggu Pembayaran</h3>
            <p class="text-muted">Total: <strong>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</strong></p>
            <p>Silahkan minta pelanggan scan QRIS yang muncul.</p>
            
            <div class="my-4">
                <button id="pay-button" class="btn btn-primary btn-lg">
                    <i class="bi bi-qr-code"></i> Tampilkan QRIS / Bayar Sekarang
                </button>
            </div>
            
            <div class="alert alert-info small">
                Status saat ini: <span class="badge bg-warning text-dark">{{ strtoupper($transaksi->status) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- 1. Load Snap JS --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script type="text/javascript">
    // Log awal untuk memastikan script terpanggil
    console.log("Script Pay QRIS dimuat...");

    const payButton = document.getElementById('pay-button');
    const snapToken = '{{ $snapToken }}';

    // Cek apakah Client Key terload
    const clientKey = "{{ config('midtrans.client_key') }}";
    console.log("Client Key:", clientKey);
    console.log("Snap Token:", snapToken);

    payButton.addEventListener('click', function (e) {
        e.preventDefault(); // Mencegah reload halaman
        console.log("Tombol Bayar Ditekan!");

        // Cek ketersediaan Snap
        if (typeof window.snap === 'undefined') {
            alert("Error: Snap.js belum dimuat. Cek koneksi internet atau Client Key.");
            return;
        }

        window.snap.pay(snapToken, {
            onSuccess: function(result){
                console.log("Success:", result);
                window.location.href = "{{ route('transaksi.finish_qris', $transaksi->id_transaksi) }}";
            },
            onPending: function(result){
                console.log("Pending:", result);
                alert("Menunggu pembayaran!");
            },
            onError: function(result){
                console.log("Error:", result);
                alert("Pembayaran gagal atau error sistem.");
            },
            onClose: function(){
                console.log("Closed");
                alert('Anda menutup popup tanpa menyelesaikan pembayaran');
            }
        });
    });
</script>
@endsection