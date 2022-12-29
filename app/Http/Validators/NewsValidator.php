<?php

namespace App\Http\Validators;

class NewsValidator {
    public static function rules(): array {
        return [
            'category_id'    => 'required',
            'subcategory_id' => 'required',
            'tags'           => 'required|array',
            'title'          => 'required',
            'content'        => 'required',
            'teaser'         => 'required',
            'published_at'   => 'required',
            'image'          => 'required|mimes:png,jpg,jpeg|max:2048',
            'image_caption'  => 'required|string'
        ];
    }
}
