<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\TuitionPlan;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $enrollments = Enrollment::all();
        $plan = TuitionPlan::first();

        if (!$plan) {
            $this->command->error('No tuition plan found. Please run TuitionPlanSeeder first.');
            return;
        }

        $user = \App\Models\User::first();
        if (!$user) {
            $this->command->error('No user found. Please run UserSeeder first.');
            return;
        }

        foreach ($enrollments as $i => $enrollment) {
            // Seed payments for about 70% of students
            if ($i % 10 < 7) {
                Payment::create([
                    'student_id' => $enrollment->student_id,
                    'enrollment_id' => $enrollment->id,
                    'tuition_plan_id' => $plan->id,
                    'amount' => $plan->price ?? 50.00,
                    'payment_date' => $enrollment->enrollment_date->addDays(rand(1, 5)),
                    'start_study_date' => $enrollment->start_date,
                    'end_study_date' => $enrollment->start_date->addMonth(),
                    'status' => 'paid',
                    'payment_method' => 'Cash',
                    'created_by' => $user->id,
                ]);
            }
        }
    }
}
