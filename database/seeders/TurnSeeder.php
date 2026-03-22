<?php

namespace Database\Seeders;

use App\Models\Turn;
use Illuminate\Database\Seeder;

class TurnSeeder extends Seeder
{
    public function run(): void
    {
        $turns = [
            ['name' => 'Morning', 'start_time' => '07:30:00', 'end_time' => '11:30:00'],
            ['name' => 'Afternoon', 'start_time' => '13:00:00', 'end_time' => '17:00:00'],
            ['name' => 'Evening', 'start_time' => '17:30:00', 'end_time' => '20:30:00'],
        ];

        foreach ($turns as $turn) {
            Turn::updateOrCreate(['name' => $turn['name']], $turn);
        }
    }
}
