<?php

namespace App\Http\Requests\auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ];
    }

    public function passwordRules()
    {
        return [
            'required',
            'min:8',
            'regex:/\d/', // number
            'regex:/[A-Z]/', // uppercase
            'regex:/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', // special character
            'confirmed',
        ];
    }
}
