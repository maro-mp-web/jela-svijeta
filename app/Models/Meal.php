<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;
    use Translatable;

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function tags(){
        return $this->belongsToMany(Tag::class);
    }
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_meal');
    }

    public function translations()
    {
        return $this->hasMany(MealTranslation::class);
    }


    public $translatedAttributes = ['title', 'description'];
}
