<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreferenceDetail extends Model
{
    protected $fillable = ['user_preference_id', 'category_id','author','source'];
    public function user_preference()
    {
        return $this->belongsTo(UserPreference::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
