<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    use Translatable;

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'ingredient_meal');
    }
    public $translatedAttributes = ['title'];
}
