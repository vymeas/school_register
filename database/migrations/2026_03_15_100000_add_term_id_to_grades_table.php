<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->unsignedBigInteger('term_id')->after('id')->nullable();
        });

        // Assign existing grades to the first term
        $firstTermId = DB::table('terms')->orderBy('id')->value('id');
        if ($firstTermId) {
            DB::table('grades')->whereNull('term_id')->update(['term_id' => $firstTermId]);
        }

        Schema::table('grades', function (Blueprint $table) {
            $table->unsignedBigInteger('term_id')->nullable(false)->change();
            $table->foreign('term_id')->references('id')->on('terms')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['term_id']);
            $table->dropColumn('term_id');
        });
    }
};
