<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_pembimbing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            // Status: pending | approved | rejected
            $table->string('status')->default('pending');
            $table->text('alasan')->nullable();           // alasan mahasiswa mengajukan
            $table->text('catatan_kaprodi')->nullable();  // catatan dari kaprodi
            $table->timestamp('diajukan_pada')->nullable();
            $table->timestamp('diproses_pada')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pembimbing');
    }
};
