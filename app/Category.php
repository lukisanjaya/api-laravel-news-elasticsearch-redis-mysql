<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'slug'];

    public function subcategory()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
