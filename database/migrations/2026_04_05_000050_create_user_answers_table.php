<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attempt_id')->constrained('user_exams')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('selected_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('time_spent_seconds')->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
