<?php

namespace App\Http\Validators;

class TagValidator {
    public static function rules(): array {
        return [
            'name' => 'required|min:3'
        ];
    }
}
