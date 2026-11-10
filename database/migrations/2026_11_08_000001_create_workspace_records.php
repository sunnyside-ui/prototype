<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('workspace_records', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->foreignId('evaluation_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('users');
            $table->string('title');
            $table->text('details')->nullable();
            $table->string('status')->nullable();
            $table->date('scheduled_on')->nullable();
            $table->timestamps();
            $table->index(['module', 'evaluation_id']);
        });
        Schema::table('users', fn (Blueprint $table) => $table->string('student_status')->default('Active'));
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_records');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('student_status'));
    }
};

