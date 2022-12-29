<?php

namespace App\Http\Validators;

class CategoryValidator {
    public static function rules(): array {
        return [
            'name' => 'required|min:3'
        ];
    }
}
