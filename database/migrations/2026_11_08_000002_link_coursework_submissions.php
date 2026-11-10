<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('workspace_records', function (Blueprint $table) {
            $table->foreignId('task_id')->nullable()->constrained('workspace_records')->restrictOnDelete();
            $table->unique('task_id');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('workspace_records', function (Blueprint $table) {
            $table->dropUnique(['task_id']);
            $table->dropConstrainedForeignId('task_id');
            $table->dropColumn(['attachment_path', 'attachment_name']);
        });
    }
};
