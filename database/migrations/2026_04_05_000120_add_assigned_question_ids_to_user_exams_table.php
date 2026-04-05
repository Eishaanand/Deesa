<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_exams', function (Blueprint $table): void {
            $table->json('assigned_question_ids')->nullable()->after('analytics');
        });
    }

    public function down(): void
    {
        Schema::table('user_exams', function (Blueprint $table): void {
            $table->dropColumn('assigned_question_ids');
        });
    }
};
