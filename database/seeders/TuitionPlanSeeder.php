<?php

namespace Database\Seeders;

use App\Models\TuitionPlan;
use Illuminate\Database\Seeder;

class TuitionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['name' => '1 Month', 'duration_month' => 1, 'price' => 200.00],
            ['name' => '3 Months', 'duration_month' => 3, 'price' => 550.00],
            ['name' => '6 Months', 'duration_month' => 6, 'price' => 1000.00],
            ['name' => '1 Year', 'duration_month' => 12, 'price' => 1800.00],
        ];

        foreach ($plans as $plan) {
            TuitionPlan::create($plan);
        }
    }
}
