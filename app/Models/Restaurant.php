<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'desc', 'address', 'img', 'code'];

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function menu(): HasOne
    {
        return $this->hasOne(Menu::class);
    }
}
