<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Superadmin sudah dicek via middleware
    }

    public function rules()
    {
        return [
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'username' => 'required|string|max:100|unique:users,username',
            'role'     => 'required|in:SUPERADMIN,ADMIN,OPERATOR,KEUANGAN,INVENTORY,SALES',
            'password' => 'required|min:6',
        ];
    }
}
