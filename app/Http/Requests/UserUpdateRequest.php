<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('user'); // id user

        return [
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $id,
            'username' => 'required|string|max:100|unique:users,username,' . $id,
            'role'     => 'required|in:SUPERADMIN,ADMIN,OPERATOR,KEUANGAN,INVENTORY,SALES',
            'password' => 'nullable|min:6',
        ];
    }
}
