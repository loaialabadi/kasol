<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OffersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $serviceIds = DB::table('services')->pluck('id');
        $userIds = DB::table('users')->pluck('id');
        $imageIds = DB::table('images')->pluck('id');

        foreach (range(1, 10) as $index) {
            DB::table('offers')->insert([
                'title' => $faker->word(),
                'description' => $faker->text(200),
                'image' => $faker->imageUrl(640, 480, 'business'),
                'discount_rate' => $faker->randomFloat(2, 5, 50),
                'start_date' => $faker->date(),
                'end_date' => $faker->date(),
                'price' => $faker->randomFloat(2, 10, 1000),
                'descounted_price' => $faker->randomFloat(2, 10, 1000),
                'stars_rate' => $faker->randomFloat(2, 1, 5),
                'created_by' => $faker->randomElement($userIds),
                'status' => $faker->randomElement(['active', 'inactive']),
                'service_id' => $faker->randomElement($serviceIds),
                'user_id' => $faker->randomElement($userIds),
                'image_id' => $faker->randomElement($imageIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
