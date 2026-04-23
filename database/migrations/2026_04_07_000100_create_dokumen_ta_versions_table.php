<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dokumen_ta_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokumen_ta_id')->constrained('dokumen_ta')->onDelete('cascade');
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('uploader_role', 50);
            $table->string('action', 50)->default('upload');
            $table->string('file')->nullable();
            $table->text('note')->nullable();
            $table->string('status_snapshot', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_ta_versions');
    }
};
