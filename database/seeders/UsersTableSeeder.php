<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // إنشاء 10 مستخدمين وهميين (اختياري)
        User::factory(10)->create();

        // إنشاء مستخدم يدويًا بكلمة مرور مشفرة
        User::create([
            'name' => 'Loiaa',
            'email' => 'loiaa@gmail.com',
            'password' => Hash::make('passwordd'), // كلمة المرور مشفرة
        ]);
    }
}
