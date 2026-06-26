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
        Schema::create('siswa_akademik', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')->constrained('siswa');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran');

            $table->enum('tingkat', ['X', 'XI', 'XII']);
            $table->foreignId('rombel_id')->constrained('rombel');
            $table->foreignId('rayon_id')->constrained('rayon');

            $table->enum('status', ['aktif', 'naik', 'alumni'])->default('aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_akademik');
    }
};
