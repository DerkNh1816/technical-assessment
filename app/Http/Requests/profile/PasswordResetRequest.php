<?php

namespace App\Http\Requests\profile;

use Illuminate\Foundation\Http\FormRequest;

final class PasswordResetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }
}
