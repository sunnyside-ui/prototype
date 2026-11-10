<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('academic_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->restrictOnDelete();
            $table->string('name');
            $table->json('periods');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_trackers');
    }
};
