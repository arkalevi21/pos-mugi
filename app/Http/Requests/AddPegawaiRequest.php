<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddPegawaiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ubah ke true agar user yang login bisa akses
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Ambil ID dari route parameter (karena di web.php pakai '/{id}')
        $id = $this->route('id');

        // Rule dasar
        $rules = [
            'nama_user' => 'required|string|max:100',
            'role'      => 'required|in:admin,kasir',
        ];

        if ($this->isMethod('post')) {
            // === VALIDASI SAAT TAMBAH DATA (STORE) ===
            $rules['username'] = 'required|string|max:50|unique:users,username';
            $rules['password'] = 'required|string|min:6|confirmed';
        } else {
            // === VALIDASI SAAT EDIT DATA (UPDATE) ===
            // unique:table,column,except_id,id_column
            // Kita harus mengecualikan ID user yang sedang diedit agar tidak dianggap duplikat
            $rules['username'] = 'required|string|max:50|unique:users,username,' . $id . ',id_user';
            $rules['password'] = 'nullable|string|min:6|confirmed';
        }

        return $rules;
    }

    /**
     * Custom pesan error (Opsional)
     */
    public function messages(): array
    {
        return [
            'nama_user.required' => 'Nama pegawai wajib diisi',
            'username.unique'    => 'Username sudah digunakan, pilih yang lain',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min'       => 'Password minimal 6 karakter'
        ];
    }
}
