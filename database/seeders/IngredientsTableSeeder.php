<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class IngredientsTableSeeder extends Seeder
{
    const NUMBER_OF_INGREDIENTS = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = $this->generateIngredientsData();

        DB::table('ingredients')->insert($ingredients['ingredients']);
        DB::table('ingredient_translations')->insert($ingredients['translations']);
    }

    /**
     * Generate ingredients data.
     */
    private function generateIngredientsData(): array
    {
        $faker = Faker::create();
        $ingredients = [];
        $translations = [];

        foreach (range(1, self::NUMBER_OF_INGREDIENTS) as $index) {
            $ingredientId = DB::table('ingredients')->insertGetId([
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach (['en', 'hr'] as $locale) {
                $translations[] = [
                    'ingredient_id' => $ingredientId,
                    'locale' => $locale,
                    'title' => $faker->word,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        return ['ingredients' => $ingredients, 'translations' => $translations];
    }
}
