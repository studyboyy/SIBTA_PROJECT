<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->foreignId('kaprodi_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->foreignId('prodi_id')->nullable()->after('prodi')->constrained('prodis')->nullOnDelete();
        });

        $existingProdis = DB::table('mahasiswas')
            ->select('prodi')
            ->whereNotNull('prodi')
            ->distinct()
            ->pluck('prodi');

        foreach ($existingProdis as $name) {
            $baseCode = Str::upper(Str::limit(Str::slug((string) $name, ''), 12, '')) ?: 'PRODI';
            $code = $baseCode;
            $suffix = 1;

            while (DB::table('prodis')->where('code', $code)->exists()) {
                $code = $baseCode . $suffix;
                $suffix++;
            }

            $prodiId = DB::table('prodis')->insertGetId([
                'name' => $name,
                'code' => $code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('mahasiswas')
                ->where('prodi', $name)
                ->update(['prodi_id' => $prodiId]);
        }
    }

    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prodi_id');
        });

        Schema::dropIfExists('prodis');
    }
};
