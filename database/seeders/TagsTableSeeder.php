<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Tag 1',
            'Tag 2',
            'Tag 3',
        ];

        $faker = Faker::create();

        foreach ($tags as $tagTitle) {
            $counter = 1;
            $slug = Str::slug($tagTitle);  // Koristi Laravelov helper za generiranje slug-ova

            // Provjerite postoji li već slog
            while (DB::table('tags')->where('slug', $slug)->exists()) {
                $slug = Str::slug($tagTitle) . '-' . $counter;  // Dodajte sufiks da biste osigurali jedinstvenost
                $counter++;
            }

            DB::table('tags')->insert([
                'slug' => $slug,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $tagId = DB::getPdo()->lastInsertId();
            foreach (['en', 'hr'] as $locale) {
                DB::table('tag_translations')->insert([
                    'tag_id' => $tagId,
                    'locale' => $locale,
                    'title' => $tagTitle,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
