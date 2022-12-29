<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    public $table = 'subcategories';
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
