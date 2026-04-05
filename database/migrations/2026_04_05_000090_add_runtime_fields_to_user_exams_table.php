<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_exams', function (Blueprint $table): void {
            $table->unsignedInteger('current_question_index')->default(0)->after('current_section_id');
            $table->unsignedInteger('section_seconds_remaining')->default(0)->after('current_question_index');
            $table->timestamp('paused_at')->nullable()->after('started_at');
            $table->timestamp('ended_at')->nullable()->after('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_exams', function (Blueprint $table): void {
            $table->dropColumn(['current_question_index', 'section_seconds_remaining', 'paused_at', 'ended_at']);
        });
    }
};
