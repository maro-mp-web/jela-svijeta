<?php

namespace App\Http\Controllers;

use App\Http\Requests\LangRequest;
use Illuminate\Http\Request;
use App\Models\Meal;
use App\Services\MealService;

class MealsController extends Controller
{
    protected $mealService;


    public function __construct(MealService $mealService)
    {
        $this->mealService = $mealService;
    }

    public function index(LangRequest $request)
    {
        $query = Meal::query();

        // Filtriranje po broju stranice i broju rezultata po stranici
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        // Filtriranje po kategoriji
        if ($request->has('category')) {
            $category = $request->input('category');
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
            $tags = explode(',', $request->input('tags'));
            foreach ($tags as $tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('tag_id', $tag);
                });
            }
        }

        // Filtriranje po jeziku
        $lang = $request->input('lang', config('app.locale'));
        app()->setLocale($lang);

        // Filtriranje po vremenu razlike
        if ($request->has('diff_time')) {
            $diffTime = $request->input('diff_time');
            $date = Carbon::createFromTimestamp($diffTime);
            $query->withTrashed()->where('updated_at', '>=', $date);
        }

        // Dodavanje dodatnih podataka u odgovor
        if ($request->has('with')) {
            $with = explode(',', $request->input('with'));
            $query->with($with);
        }

        // Dohvaćanje rezultata
        $meals = $query->paginate($request->input('per_page', 15));

        $response = [
            'meta' => [
                'currentPage' => $meals->currentPage(),
                'totalItems' => $meals->total(),
                'itemsPerPage' => $meals->perPage(),
                'totalPages' => $meals->lastPage(),
            ],
            'data' => $meals->map(function ($meal) use ($request) {
                return [
                    'id' => $meal->id,
                    'title' => $meal->translate($request->input('lang'))->title,
                    'description' => $meal->translate($request->input('lang'))->description,
                    'status' => $this->getStatus($meal, $request->input('diff_time')),
                    'category' => optional($meal->category)->only(['id', 'title', 'slug']),
                    'tags' => $meal->tags->map->only(['id', 'title', 'slug']),
                    'ingredients' => $meal->ingredients->map->only(['id', 'title', 'slug']),
                ];
            }),
            'links' => [
                'prev' => $meals->previousPageUrl(),
                'next' => $meals->nextPageUrl(),
                'self' => $meals->url($meals->currentPage()),
            ],
        ];

        $lang = $request->validated()['lang'] ?? config('app.locale');
        app()->setLocale($lang);

        // Dohvaćanje rezultata
        $meals = $query->paginate($perPage);

        $response = [
            'meta' => [
                'currentPage' => $meals->currentPage(),
                'totalItems' => $meals->total(),
                'itemsPerPage' => $meals->perPage(),
                'totalPages' => $meals->lastPage(),
            ],
            'data' => $meals->map(function ($meal) use ($request) {
                return [
                    'id' => $meal->id,
                    'title' => $meal->getTranslation('title', $request->input('lang', config('app.locale'))),
                    'description' => $meal->getTranslation('description', $request->input('lang', config('app.locale'))),
                    'status' => $this->getStatus($meal, $request->input('diff_time')),
                    'category' => $meal->category ? [
                        'id' => $meal->category->id,
                        'title' => $meal->category->getTranslation('title', $request->input('lang', config('app.locale'))),
                        'slug' => $meal->category->slug,
                    ] : null,
                    'tags' => $meal->tags->map(function ($tag) use ($request) {
                        return [
                            'id' => $tag->id,
                            'title' => $tag->getTranslation('title', $request->input('lang', config('app.locale'))),
                            'slug' => $tag->slug,
                        ];
                    }),
                    'ingredients' => $meal->ingredients->map(function ($ingredient) use ($request) {
                        return [
                            'id' => $ingredient->id,
                            'title' => $ingredient->getTranslation('title', $request->input('lang', config('app.locale'))),
                            'slug' => $ingredient->slug,
                        ];
                    }),
                ];
            }),
            'links' => [
                'prev' => $meals->previousPageUrl(),
                'next' => $meals->nextPageUrl(),
                'self' => $meals->url($meals->currentPage()),
            ],
        ];

        return response()->json($response);
    }

    protected function getStatus(Meal $meal, $diffTime)
    {
        if ($diffTime) {
            $date = Carbon::createFromTimestamp($diffTime);
            if ($meal->created_at > $date) {
                return 'created';
            } elseif ($meal->updated_at > $date) {
                return 'modified';
            } elseif ($meal->deleted_at && $meal->deleted_at > $date) {
                return 'deleted';
            }
        }

        return 'created';
    }

}
