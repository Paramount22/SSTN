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
     * Creates the single administrator account (local development password –
     * change it before the first deployment) and the current site content.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@sstn.test'],
            [
                'name' => 'Admin SSTN',
                'password' => Hash::make('Sstn-dev-2026!'),
                'email_verified_at' => now(),
                'is_admin' => true,
            ],
        );

        $this->call([
            TextBlockSeeder::class,
            MembershipFeeSeeder::class,
            LinkSeeder::class,
            EventSeeder::class,
        ]);
    }
}
