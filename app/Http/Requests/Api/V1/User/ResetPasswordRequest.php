<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required','email'],
            'token' => ['required'],
            'password' => ['required','confirmed','min:6'],
        ];
    }
}