<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->foreignId('id_user')->constrained('users', 'id_user');
            $table->string('nama_pembeli', 100);
            $table->enum('metode_pembayaran', ['tunai', 'qris']);
            $table->integer('uang_diterima')->nullable(); // untuk tunai
            $table->integer('uang_kembalian')->nullable(); // untuk tunai
            $table->string('midtrans_order_id')->nullable(); // untuk qris
            $table->string('midtrans_status')->nullable(); // untuk qris
            $table->integer('total_harga');
            $table->timestamp('tanggal')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
