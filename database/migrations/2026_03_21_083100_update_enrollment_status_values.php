<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE enrollments MODIFY status ENUM('pending','active','blocked','inactive','transferred') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE enrollments MODIFY status ENUM('active','inactive','transferred') DEFAULT 'active'");
        }
    }
};
