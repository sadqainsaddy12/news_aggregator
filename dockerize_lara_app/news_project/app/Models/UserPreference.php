<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = ['user_id', 'name'];

    public function details(){
        return $this->hasMany(UserPreferenceDetail::class);
    }
}
