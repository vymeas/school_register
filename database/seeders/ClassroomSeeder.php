<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Turn;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        $grade1 = Grade::where('name', 'Grade 1')->first();
        $grade2 = Grade::where('name', 'Grade 2')->first();
        $morning = Turn::where('name', 'Morning')->first();
        $afternoon = Turn::where('name', 'Afternoon')->first();
        $evening = Turn::where('name', 'Evening')->first();

        if (!$grade1 || !$grade2 || !$morning || !$afternoon || !$evening) {
            $this->command->error('Grades or Turns not found. Please run GradeSeeder and TurnSeeder first.');
            return;
        }

        $classrooms = [
            // Grade 1
            ['name' => '1A', 'grade_id' => $grade1->id, 'turn_id' => $morning->id, 'capacity' => 35],
            ['name' => '1B', 'grade_id' => $grade1->id, 'turn_id' => $morning->id, 'capacity' => 35],
            ['name' => '1C', 'grade_id' => $grade1->id, 'turn_id' => $morning->id, 'capacity' => 35],
            ['name' => '1D', 'grade_id' => $grade1->id, 'turn_id' => $morning->id, 'capacity' => 35],
            ['name' => '1E', 'grade_id' => $grade1->id, 'turn_id' => $morning->id, 'capacity' => 35],
            ['name' => '1F', 'grade_id' => $grade1->id, 'turn_id' => $evening->id, 'capacity' => 30],
            ['name' => '1G', 'grade_id' => $grade1->id, 'turn_id' => $evening->id, 'capacity' => 30],
            ['name' => '1H', 'grade_id' => $grade1->id, 'turn_id' => $evening->id, 'capacity' => 30],
            ['name' => '1I', 'grade_id' => $grade1->id, 'turn_id' => $evening->id, 'capacity' => 30],
            ['name' => '1J', 'grade_id' => $grade1->id, 'turn_id' => $evening->id, 'capacity' => 30],

            // Grade 2
            ['name' => '2A', 'grade_id' => $grade2->id, 'turn_id' => $morning->id, 'capacity' => 35],
            ['name' => '2B', 'grade_id' => $grade2->id, 'turn_id' => $afternoon->id, 'capacity' => 30],
            ['name' => '2C', 'grade_id' => $grade2->id, 'turn_id' => $afternoon->id, 'capacity' => 30],
        ];

        foreach ($classrooms as $classroom) {
            Classroom::updateOrCreate(['name' => $classroom['name'], 'grade_id' => $classroom['grade_id']], $classroom);
        }
    }
}
