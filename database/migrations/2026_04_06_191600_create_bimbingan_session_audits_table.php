<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bimbingan_session_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bimbingan_log_id')->constrained('bimbingan_logs')->onDelete('cascade');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status_sesi', 20)->nullable();
            $table->string('to_status_sesi', 20);
            $table->string('source', 30)->default('manual');
            $table->string('note')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bimbingan_session_audits');
    }
};
