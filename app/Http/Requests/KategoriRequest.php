<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori,' . $this->route('id') . ',id_kategori'
        ];
    }
    
    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori harus diisi',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan'
        ];
    }
}
