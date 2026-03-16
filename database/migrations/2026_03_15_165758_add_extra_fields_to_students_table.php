<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('place_of_birth')->nullable()->after('date_of_birth');
            $table->string('father_name')->nullable()->after('parent_phone');
            $table->string('father_contact')->nullable()->after('father_name');
            $table->string('mother_name')->nullable()->after('father_contact');
            $table->string('mother_contact')->nullable()->after('mother_name');
            $table->text('characteristics')->nullable()->after('address');
            $table->text('health')->nullable()->after('characteristics');
            $table->string('emergency_name')->nullable()->after('emergency_contact');
            $table->string('turn')->nullable()->after('term_id');
            $table->string('time')->nullable()->after('turn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'place_of_birth',
                'father_name',
                'father_contact',
                'mother_name',
                'mother_contact',
                'characteristics',
                'health',
                'emergency_name',
                'turn',
                'time'
            ]);
        });
    }
};
