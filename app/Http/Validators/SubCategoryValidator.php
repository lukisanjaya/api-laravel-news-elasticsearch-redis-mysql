<?php

namespace App\Http\Validators;

class SubCategoryValidator {
    public static function rules(): array {
        return [
            'category_id' => 'required',
            'name'        => 'required|min:3'
        ];
    }
}
