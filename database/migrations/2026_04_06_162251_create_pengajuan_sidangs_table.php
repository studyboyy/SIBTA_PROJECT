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
        Schema::create('pengajuan_sidangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending | revisi | approved | rejected
            $table->text('catatan_mahasiswa')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamp('diajukan_pada')->nullable();
            $table->timestamps();

            $table->unique('mahasiswa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_sidangs');
    }
};
