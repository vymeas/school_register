<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $term = \App\Models\Term::first();
        if (!$term) {
            $this->command->error('No terms found. Please run TermSeeder first.');
            return;
        }

        $grades = [
            ['term_id' => $term->id, 'name' => 'Grade 1', 'description' => 'First grade'],
            ['term_id' => $term->id, 'name' => 'Grade 2', 'description' => 'Second grade'],
            ['term_id' => $term->id, 'name' => 'Grade 3', 'description' => 'Third grade'],
            ['term_id' => $term->id, 'name' => 'Grade 4', 'description' => 'Fourth grade'],
            ['term_id' => $term->id, 'name' => 'Grade 5', 'description' => 'Fifth grade'],
            ['term_id' => $term->id, 'name' => 'Grade 6', 'description' => 'Sixth grade'],
        ];

        foreach ($grades as $grade) {
            Grade::create($grade);
        }
    }
}
