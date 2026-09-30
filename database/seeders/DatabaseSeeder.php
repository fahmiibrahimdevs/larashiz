<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            LaratrustSeeder::class,
            PostSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'fahmi@admin.com'],
            [
                'name' => 'Fahmi Admin',
                'password' => '1',
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        $user = User::firstOrCreate(
            ['email' => 'fahmi@user.com'],
            [
                'name' => 'Fahmi User',
                'password' => '1',
                'is_active' => false,
            ]
        );
        $user->syncRoles(['user']);
    }
}
