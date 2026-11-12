<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Creates the local development admin account. Change the password
     * before the first deployment; it is for local use only.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@sstn.test'],
            [
                'name' => 'Admin SSTN',
                'password' => Hash::make('Sstn-dev-2026!'),
                'email_verified_at' => now(),
            ],
        );
    }
}
