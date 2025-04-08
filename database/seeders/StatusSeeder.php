<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('statuses')->insert([
            [
                'id' => 1,
                'name' => 'pending',
                'title' => 'Не подтверждён',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'confirmed',
                'title' => 'Подтвержден',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'failed',
                'title' => 'Ошибка',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
