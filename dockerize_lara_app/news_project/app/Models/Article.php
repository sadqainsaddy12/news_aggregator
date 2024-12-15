<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'content',
        'author',
        'category_id',
        'source',
        'published_date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
