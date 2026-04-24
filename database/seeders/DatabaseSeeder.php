<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'dani@hawkins.es'],
            [
                'name' => 'Dani Hawkins',
                'password' => Hash::make('Hawkins2025!'),
                'locale' => 'es',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'juancarlos@euroship.es'],
            [
                'name' => 'Juan Carlos Euroship',
                'password' => Hash::make('Euroship2025!'),
                'locale' => 'es',
                'email_verified_at' => now(),
            ]
        );
    }
}
