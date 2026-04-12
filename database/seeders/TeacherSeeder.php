<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            [
                'teacher_code' => 'TCH001',
                'name' => 'សុខ ចាន់ដារ៉ា',
                'gender' => 'Male',
                'phone' => '012345678',
                'status' => 'active',
                'address' => 'Phnom Penh',
                'hire_date' => '2025-01-01',
            ],
            [
                'teacher_code' => 'TCH002',
                'name' => 'ជា ស្រីអូន',
                'gender' => 'Female',
                'phone' => '012345679',
                'status' => 'active',
                'address' => 'Phnom Penh',
                'hire_date' => '2025-01-10',
            ],
            [
                'teacher_code' => 'TCH003',
                'name' => 'លី វីបុល',
                'gender' => 'Male',
                'phone' => '012345680',
                'status' => 'active',
                'address' => 'Kandal',
                'hire_date' => '2025-02-01',
            ],
            [
                'teacher_code' => 'TCH004',
                'name' => 'សៅ ធារី',
                'gender' => 'Female',
                'phone' => '012345681',
                'status' => 'active',
                'address' => 'Phnom Penh',
                'hire_date' => '2025-02-15',
            ],
        ];

        foreach ($teachers as $teacher) {
            Teacher::updateOrCreate(['teacher_code' => $teacher['teacher_code']], $teacher);
        }
    }
}
