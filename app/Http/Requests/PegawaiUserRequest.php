<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PegawaiUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_pegawai' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'pegawai_is_active' => ['nullable', 'boolean'],
            
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($this->userIdForIgnore(), 'id'),
            ],
            'password' => [
                $this->route('pegawai') ? 'nullable' : 'required',
                'string',
                'min:6',
                'confirmed',
            ],
            'groupfk' => ['required', 'exists:groups_m,id'],
            'is_superadmin' => ['nullable', 'boolean'],
            'user_is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pegawai.required' => 'Nama pegawai wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip, dan underscore.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi untuk akun baru.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'groupfk.required' => 'Group/hak akses wajib dipilih.',
            'groupfk.exists' => 'Group tidak valid.',
        ];
    }
    
    protected function userIdForIgnore(): ?int
    {
        $pegawai = $this->route('pegawai');

        return $pegawai?->users?->first()?->id;
    }
}