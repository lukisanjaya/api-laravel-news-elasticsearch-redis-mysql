<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsTag extends Model
{
    public    $timestamps = false;
    protected $fillable   = ['tag_id', 'news_id'];
    public    $table      = 'news_tags';

    public function tag()
    {
        return $this->hasMany(Tag::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
