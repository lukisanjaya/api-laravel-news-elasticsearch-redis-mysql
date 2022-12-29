<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class News extends Model
{
    use SoftDeletes;
    protected $table = 'news';
    protected $fillable = [
        'author_id',
        'category_id',
        'subcategory_id',
        'title',
        'slug',
        'content',
        'teaser',
        'image',
        'image_caption',
        'published_at'
    ];

    public function setSlugAttribute($slug)
    {
        return $this->attributes['slug'] = Str::slug($slug);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function tag()
    {
        return $this->belongsToMany(Tag::class, 'news_tags');
    }
}
