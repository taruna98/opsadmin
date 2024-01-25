<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'user name',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => '11',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
