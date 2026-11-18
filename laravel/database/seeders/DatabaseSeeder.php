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
     * Creates the current site content and, on local and test machines only,
     * a development administrator. Its password is public in Git, so any other
     * environment gets its admin from `php artisan admin:create` instead.
     */
    public function run(): void
    {
        if (app()->environment(['local', 'testing'])) {
            $admin = User::query()->updateOrCreate(
                ['email' => 'admin@sstn.test'],
                [
                    'name' => 'Admin SSTN',
                    'password' => Hash::make('Sstn-dev-2026!'),
                    'email_verified_at' => now(),
                ],
            );
            $admin->forceFill(['is_admin' => true])->save();
        } else {
            $this->command?->warn('Vývojový administrátor sa mimo lokálneho prostredia nevytvára. Použite: php artisan admin:create');
        }

        $this->call([
            TextBlockSeeder::class,
            MembershipFeeSeeder::class,
            LinkSeeder::class,
            EventSeeder::class,
        ]);
    }
}
