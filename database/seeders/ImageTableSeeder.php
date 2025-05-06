<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ImageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $serviceIds = DB::table('services')->pluck('id')->toArray();
        foreach (range(1, 10) as $index) {
            DB::table('images')->insert([
                'service_id' => $faker->randomElement($serviceIds), 
                'image' => $faker->imageUrl(640, 480, 'business'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
