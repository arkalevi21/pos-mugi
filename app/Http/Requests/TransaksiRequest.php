<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'nama_pembeli' => 'required|string|max:100',
            'metode_pembayaran' => 'required|in:tunai,qris',
            'uang_diterima' => 'required_if:metode_pembayaran,tunai|integer|min:0',
            'total_harga' => 'required|integer|min:0',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produk,id_produk',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|integer|min:0',
            'items.*.subtotal' => 'required|integer|min:0'
        ];
    }
    
    public function messages(): array
    {
        return [
            'nama_pembeli.required' => 'Nama pembeli harus diisi',
            'metode_pembayaran.required' => 'Metode pembayaran harus dipilih',
            'uang_diterima.required_if' => 'Uang diterima harus diisi untuk pembayaran tunai',
            'items.required' => 'Minimal ada satu produk dalam keranjang',
            'items.*.id_produk.exists' => 'Produk tidak ditemukan',
            'items.*.qty.min' => 'Jumlah produk minimal 1'
        ];
    }
}