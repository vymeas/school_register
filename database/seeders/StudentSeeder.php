<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Classroom;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $classrooms = Classroom::all();
        $term = Term::where('status', 'active')->first() ?? Term::first();

        if ($classrooms->isEmpty()) {
            $this->command->error('No classrooms found. Please run ClassroomSeeder first.');
            return;
        }

        $khmerLastNames = ['សោម', 'កែវ', 'សុខ', 'ចាន់', 'លី', 'ជា', 'ហេង', 'សៅ', 'ម៉ៅ', 'លឹម'];
        $khmerFirstNames = ['សុភ័ក្ត្រ', 'ស្រីនិ', 'រតនា', 'វិបុល', 'មករា', 'ធារី', 'ណារ៉េត', 'សិលា', 'វណ្ណះ', 'បូរមី'];

        for ($i = 1; $i <= 50; $i++) {
            $firstName = $khmerFirstNames[array_rand($khmerFirstNames)];
            $lastName = $khmerLastNames[array_rand($khmerLastNames)];
            $gender = $i % 2 == 0 ? 'Female' : 'Male';
            $classroom = $classrooms->random();
            $code = 'STU' . str_pad($i, 4, '0', STR_PAD_LEFT);

            Student::updateOrCreate(['student_code' => $code], [
                'first_name' => $lastName,
                'last_name' => $firstName,
                'gender' => $gender,
                'date_of_birth' => now()->subYears(rand(6, 15))->format('Y-m-d'),
                'place_of_birth' => 'Phnom Penh',
                'parent_name' => $lastName . ' ' . $khmerFirstNames[array_rand($khmerFirstNames)],
                'parent_phone' => '0' . rand(11, 99) . rand(100000, 999999),
                'address' => 'Phnom Penh, Cambodia',
                'classroom_id' => $classroom->id,
                'term_id' => $term->id ?? null,
                'status' => 'active',
                'study_status' => 'studying',
                'registration_date' => now()->subMonths(rand(1, 6))->format('Y-m-d'),
            ]);
        }
    }
}
