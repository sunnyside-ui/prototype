<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student');
        });

        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('instructor_id')->constrained('users');
            $table->string('subject_code');
            $table->string('subject');
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('feedback')->nullable();
            $table->string('status')->default('draft');
            $table->text('review_note')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'subject_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('role'));
    }
};

