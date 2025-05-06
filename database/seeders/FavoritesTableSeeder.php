<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FavoritesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
        public function run()
        {
            $faker = Faker::create();

            // Fetching random ids from the users and services tables
            $userIds = DB::table('users')->pluck('id');
            $serviceIds = DB::table('services')->pluck('id');

            // Insert 10 favorite records
            foreach (range(1, 10) as $index) {
                DB::table('favorites')->insert([
                    'user_id' => $faker->randomElement($userIds),  // Random user_id
                    'service_id' => $faker->randomElement($serviceIds),  // Random service_id
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
}
