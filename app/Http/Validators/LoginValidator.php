<?php

namespace App\Http\Validators;

class LoginValidator {
    public static function rules(): array {
        return [
            'email'    => 'required|min:3',
            'password' => 'required|min:3'
        ];
    }
}
