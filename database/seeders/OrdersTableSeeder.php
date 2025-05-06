<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Ensure there are records in the related tables
        $userIds = DB::table('users')->pluck('id');
        $cartIds = DB::table('carts')->pluck('id');

        // If the related tables don't have records, seed them
        if ($userIds->isEmpty() || $cartIds->isEmpty()) {
            $this->call([
                UsersTableSeeder::class,  // Ensure users are seeded
                CartsTableSeeder::class,  // Ensure carts are seeded
            ]);
        }

        // Insert 10 orders with valid foreign keys
        foreach (range(1, 10) as $index) {
            DB::table('orders')->insert([
                'user_id' => $faker->randomElement($userIds),  // Random user_id
                'cart_id' => $faker->randomElement($cartIds),  // Random cart_id
                'status' => $faker->randomElement(['pending', 'accepted', 'processing', 'shipped', 'delivered', 'cancelled']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
