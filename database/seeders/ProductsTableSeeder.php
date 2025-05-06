<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Size;

class ProductsTableSeeder extends Seeder
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
        $subCategoryIds = DB::table('sub_categories')->pluck('id');
        $serviceIds = DB::table('services')->pluck('id');
        $userIds = DB::table('users')->pluck('id');
        $addIds = DB::table('adds')->pluck('id');

        if (
            $imageIds->isEmpty() || 
            $subCategoryIds->isEmpty() || 
            $serviceIds->isEmpty() || 
            $userIds->isEmpty() || 
            $addIds->isEmpty()
        ) {
            $this->call([
                ImageTableSeeder::class,
                CategoriesTableSeeder::class,
                ServicesTableSeeder::class,
                UsersTableSeeder::class,
                AddsTableSeeder::class,
            ]);
        }

        if (Size::count() === 0) {
            $this->call(SizeSeeder::class);
        }

        $sizeIds = Size::pluck('id')->toArray();

        foreach (range(1, 10) as $index) {
            $productId = DB::table('products')->insertGetId([
                'name' => $faker->word(),
                'slug' => $faker->slug(),
                'description' => $faker->text(200),
                'image' => $faker->imageUrl(640, 480, 'business'),
                'price' => $faker->randomFloat(2, 10, 1000),
                'status' => $faker->randomElement(['active', 'inactive']),
                'image_id' => $faker->randomElement($imageIds),
                'sub_category_id' => $faker->randomElement($subCategoryIds),
                'service_id' => $faker->randomElement($serviceIds),
                'user_id' => $faker->randomElement($userIds),
                'add_id' => $faker->randomElement($addIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $assignedSizes = $faker->randomElements($sizeIds, rand(1, count($sizeIds)));
            DB::table('product_size')->insert(
                array_map(function ($sizeId) use ($productId) {
                    return [
                        'product_id' => $productId,
                        'size_id' => $sizeId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $assignedSizes)
            );
        }
    }
}
