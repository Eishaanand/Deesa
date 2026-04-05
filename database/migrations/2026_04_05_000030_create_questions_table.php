<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('mcq');
            $table->longText('stem');
            $table->longText('passage')->nullable();
            $table->json('options');
            $table->string('correct_answer');
            $table->text('explanation')->nullable();
            $table->string('difficulty')->default('medium');
            $table->string('topic')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('sequence')->default(1);
            $table->string('source')->default('manual');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
