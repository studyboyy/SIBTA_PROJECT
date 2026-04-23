<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sidang_batches', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruangan', 100);
            $table->unsignedInteger('gelombang')->default(1);
            $table->unsignedInteger('kuota')->default(20);
            $table->foreignId('ketua_sidang_id')->constrained('dosens')->cascadeOnDelete();
            $table->foreignId('penguji_1_id')->constrained('dosens')->cascadeOnDelete();
            $table->foreignId('penguji_2_id')->constrained('dosens')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('sidangs', function (Blueprint $table) {
            $table->foreignId('sidang_batch_id')->nullable()->after('id')->constrained('sidang_batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sidangs', function (Blueprint $table) {
            $table->dropForeign(['sidang_batch_id']);
            $table->dropColumn('sidang_batch_id');
        });

        Schema::dropIfExists('sidang_batches');
    }
};
