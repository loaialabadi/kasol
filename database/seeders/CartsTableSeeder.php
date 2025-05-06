<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CartsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Ensure you have at least one record in the users and products tables
        $userIds = DB::table('users')->pluck('id');
        $productIds = DB::table('products')->pluck('id');

        // If the related tables don't have records, seed them
        if ($userIds->isEmpty() || $productIds->isEmpty()) {
            $this->call([
                UsersTableSeeder::class,  // Ensure the Users table is seeded
                ProductsTableSeeder::class,  // Ensure the Products table is seeded
            ]);
        }

        // Insert 10 records into the carts table
        foreach (range(1, 10) as $index) {
            DB::table('carts')->insert([
                'user_id' => $faker->randomElement($userIds),  // Random user_id
                'product_id' => $faker->randomElement($productIds),  // Random product_id
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
