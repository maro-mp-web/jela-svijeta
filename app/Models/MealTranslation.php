<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealTranslation extends Model
{
    use HasFactory;

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    protected $table = 'meal_translations';

    public $timestamps = false;

    protected $fillable = [
        'meal_id',
        'locale',
        'title',
        'description',
    ];

}
