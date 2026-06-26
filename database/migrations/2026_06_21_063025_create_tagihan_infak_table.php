<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_infak', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_akademik_id')
                ->constrained('siswa_akademik')
                ->onDelete('cascade');

            $table->string('periode');
            // format: 2026-07

            $table->integer('nominal');

            $table->enum('status', ['belum', 'sebagian', 'lunas'])
                ->default('belum');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_infak');
    }
};
