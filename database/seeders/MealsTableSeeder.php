<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class MealsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Dohvatite sve ID-ove kategorija iz baze podataka
        $categoryIds = DB::table('categories')->pluck('id')->toArray();

        foreach (range(1, 10) as $index) {
            // Nasumično odaberite category_id iz dostupnih ID-ova ili postavite na null
            $categoryId = $faker->randomElement(array_merge([null], $categoryIds));

            DB::table('meals')->insert([
                'category_id' => $categoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $mealId = DB::getPdo()->lastInsertId();
            foreach (['en', 'hr'] as $locale) {
                DB::table('meal_translations')->insert([
                    'meal_id' => $mealId,
                    'locale' => $locale,
                    'title' => $faker->sentence,
                    'description' => $faker->paragraph,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
