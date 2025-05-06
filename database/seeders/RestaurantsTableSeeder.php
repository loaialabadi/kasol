<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RestaurantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $imageIds = DB::table('images')->pluck('id');

        foreach (range(1, 10) as $index) {
            $restaurantId = DB::table('restaurants')->insertGetId([
                'name' => $faker->unique()->company(), 
                'address' => $faker->address(),
                'phone' => $faker->phoneNumber(), 
                'email' => $faker->email(),
                'baner' => $faker->imageUrl(800, 400, 'business'),
                'logo' => $faker->imageUrl(400, 400, 'business'), 
                'menu' => $faker->imageUrl(600, 400, 'business'),
                'status' => $faker->randomElement(['active', 'inactive']),
                'description' => $faker->text(200),
                'start_work_date' => $faker->time(),
                'end_work_date' => $faker->time(), 
                'price_range' => $faker->word(), 
                'image_id' => $faker->randomElement($imageIds), 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach (range(1, rand(1, 5)) as $branchIndex) {
                DB::table('branches')->insert([
                    'restaurant_id' => $restaurantId, 
                    'name' => $faker->unique()->company() . ' Branch', 
                    'address' => $faker->address(),
                    'phone' => $faker->phoneNumber(), 
                    'start_work_date' => $faker->time(),
                    'end_work_date' => $faker->time(), 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
