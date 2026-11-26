<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Local-only admin for the Filament panel:
 *   php artisan db:seed --class=LocalAdminSeeder
 * Login: admin@example.test / local-admin-pass-1 (development databases only).
 */
class LocalAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            $this->command?->warn('Skipped: LocalAdminSeeder only runs in local/testing.');

            return;
        }

        User::updateOrCreate(
            ['email' => 'admin@example.test'],
            ['name' => 'Admin', 'password' => 'local-admin-pass-1', 'email_verified_at' => now()]
        )->forceFill(['is_admin' => true])->save();
    }
}
