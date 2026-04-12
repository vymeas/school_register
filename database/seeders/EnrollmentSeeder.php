<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();

        foreach ($students as $student) {
            if (!$student->classroom_id) continue;

            Enrollment::updateOrCreate([
                'student_id' => $student->id,
                'classroom_id' => $student->classroom_id,
                'term_id' => $student->term_id,
            ], [
                'grade_id' => $student->classroom->grade_id ?? null,
                'enrollment_date' => $student->registration_date ?? now(),
                'start_date' => $student->registration_date ?? now(),
                'is_current' => true,
                'status' => 'active',
            ]);
        }
    }
}
