<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            StatusSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.ru',
            'password' => bcrypt('admin'),
            'phone' => '123456789',
            'address' => '123',
            'is_admin' => 1,
        ]);
    }
}
