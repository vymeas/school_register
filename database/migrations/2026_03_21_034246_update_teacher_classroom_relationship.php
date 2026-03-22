<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add teacher_id to classrooms
        Schema::table('classrooms', function (Blueprint $table) {
            $table->unsignedBigInteger('teacher_id')->nullable()->after('turn_id');
            $table->foreign('teacher_id')->references('id')->on('teachers')->nullOnDelete();
        });

        // Remove classroom_id from teachers
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });
    }

    public function down(): void
    {
        // Restore classroom_id to teachers
        Schema::table('teachers', function (Blueprint $table) {
            $table->unsignedBigInteger('classroom_id')->nullable()->after('email');
            $table->foreign('classroom_id')->references('id')->on('classrooms')->nullOnDelete();
        });

        // Remove teacher_id from classrooms
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }
};
