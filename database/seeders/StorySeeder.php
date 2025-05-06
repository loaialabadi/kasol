<?php

namespace Database\Seeders;

use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class StorySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $user = User::inRandomOrder()->first();
        $serviceIds = DB::table('services')->pluck('id')->toArray();

        if ($user) {
            // vedio
            Story::create([
                'service_id' => $faker->randomElement($serviceIds),
                'user_id' => $user->id,
                'file' => 'example-file.mp4',
                'file_path' => 'stories/example-file.mp4',
            ]);
            // image
            Story::create([
                'service_id' => $faker->randomElement($serviceIds),
                'user_id' => $user->id,
                'file' => 'another-example.jpg',
                'file_path' => 'stories/another-example.jpg',
            ]);
        }
    }
}

