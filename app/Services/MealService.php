<?php

namespace App\Services;

use App\Models\Meal;
use Illuminate\Http\Request;

class MealService
{
    protected $meal;

    public function __construct(Meal $meal)
    {
        $this->meal = $meal;
    }

    public function getMeals(Request $request)
    {
        $query = $this->meal::query();

        // Filtriranje po kategoriji
        if ($request->has('category')) {
            $category = $request->category;
            if ($category === 'NULL') {
                $query->whereNull('category_id');
            } elseif ($category === '!NULL') {
                $query->whereNotNull('category_id');
            } else {
                $query->where('category_id', $category);
            }
        }

        // Filtriranje po tagovima
        if ($request->has('tags')) {
            $tags = explode(',', $request->tags);
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('tags.id', $tags);
            });
        }

        // Dodavanje dodatnih podataka u odgovor
        if ($request->has('with')) {
            $with = explode(',', $request->with);
            $query->with($with);
        }

        // Filtriranje po jeziku
        if ($request->has('lang')) {
            // Pretpostavka je da ste postavili lokalnu aplikaciju
            app()->setLocale($request->lang);
        }

        // Filtriranje po vremenu izmjene
        if ($request->has('diff_time') && $request->diff_time > 0) {
            $timestamp = $request->diff_time;
            $query->withTrashed()
                ->where(function($q) use ($timestamp) {
                    $q->where('created_at', '>=', $timestamp)
                        ->orWhere('updated_at', '>=', $timestamp)
                        ->orWhere('deleted_at', '>=', $timestamp);
                });
        }

        // Paginacija rezultata
        return $query->paginate($request->input('per_page', 15));
    }
}
