<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('grade_id')->nullable()->after('term_id')->constrained('grades')->nullOnDelete();
            $table->date('start_date')->nullable()->after('status');
            $table->date('end_date')->nullable()->after('start_date');
            $table->boolean('is_current')->default(false)->after('end_date');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('UPDATE enrollments e INNER JOIN classrooms c ON c.id = e.classroom_id SET e.grade_id = c.grade_id');
            DB::statement("UPDATE enrollments SET start_date = COALESCE(start_date, enrollment_date), is_current = 0");
            DB::statement("ALTER TABLE enrollments MODIFY status ENUM('pending','active','completed','transferred') DEFAULT 'pending'");
        }

        $studentIds = DB::table('enrollments')
            ->select('student_id')
            ->groupBy('student_id')
            ->pluck('student_id');

        foreach ($studentIds as $studentId) {
            $currentEnrollmentId = DB::table('enrollments')
                ->where('student_id', $studentId)
                ->orderByDesc('start_date')
                ->orderByDesc('id')
                ->value('id');

            if ($currentEnrollmentId) {
                DB::table('enrollments')->where('id', $currentEnrollmentId)->update(['is_current' => true]);
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE enrollments MODIFY status ENUM('active','inactive','transferred') DEFAULT 'active'");
        }

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropColumn(['grade_id', 'start_date', 'end_date', 'is_current']);
        });
    }
};
