<?php

namespace App\Services;

use App\Models\Student;

class StudentCodeGenerator
{
    /**
     * Generate the next student code (STU-0001, STU-0002, etc.)
     */
    public static function generate(): string
    {
        $lastStudent = Student::orderBy('id', 'desc')->first();

        if ($lastStudent && preg_match('/STU-(\d+)/', $lastStudent->student_code, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return 'STU-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
