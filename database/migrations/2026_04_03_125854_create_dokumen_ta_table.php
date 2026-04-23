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
        Schema::create('dokumen_ta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained()->onDelete('cascade');
            $table->string('bab')->nullable();
            $table->string('file')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_ta');
    }
};
