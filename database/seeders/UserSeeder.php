<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => Str::ulid(),
            'name' => 'admin',
            'email' => 'admin@ifump.net',
            'password' => Hash::make('password')
        ]);
        User::create([
            'id' => Str::ulid(),
            'name' => 'muhajir',
            'email' => 'MuhajirKelana48@gmail.com',
            'password' => Hash::make('multiTech')
        ]);
    }
}
