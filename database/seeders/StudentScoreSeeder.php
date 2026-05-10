<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentScore;
use Illuminate\Database\Seeder;

class StudentScoreSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        
        foreach ($students as $student) {
            if (!$student->term_id || !$student->classroom_id) continue;
            
            StudentScore::updateOrCreate([
                'student_id' => $student->id,
                'term_id' => $student->term_id,
                'classroom_id' => $student->classroom_id,
                'academic_year' => '2025-2026'
            ], [
                'math_score' => rand(40, 100),
                'khmer_score' => rand(40, 100),
                'science_score' => rand(40, 100),
                'sociology_score' => rand(40, 100),
                'remark' => 'Seeded score'
            ]);
        }
    }
}
