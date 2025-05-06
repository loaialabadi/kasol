<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Size;

class SizeSeeder extends Seeder
{
    public function run()
    {
        $sizes = [
            ['name' => 'Small', 'slug' => 'small'],
            ['name' => 'Medium', 'slug' => 'medium'],
            ['name' => 'Large', 'slug' => 'large'],
            ['name' => 'Extra Large', 'slug' => 'extra-large'],
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
}

