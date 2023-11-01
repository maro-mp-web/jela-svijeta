<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('meals')->delete();
        DB::table('categories')->delete();

        $categories = [
            'Kategorija 1',
            'Kategorija 2',
            'Kategorija 3',
        ];

        foreach ($categories as $categoryTitle) {
            $category = Category::create([  // Koristite Eloquent model za stvaranje novih zapisa u bazi podataka
                'slug' => Str::slug($categoryTitle, '-'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach (['en', 'hr'] as $locale) {
                $category->translations()->create([  // Koristite relaciju prijevoda da biste dodali prijevode
                    'locale' => $locale,
                    'title' => $categoryTitle,
                    'slug' => Str::slug($categoryTitle, '-'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
