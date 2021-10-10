<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'email' => 'nmt.060708@gmail.com',
            'name' => 'Tuan',
            'password' => Hash::make('123456'),
        ]);
    }
}
