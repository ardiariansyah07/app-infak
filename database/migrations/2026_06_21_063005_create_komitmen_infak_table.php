<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komitmen_infak', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_akademik_id')
                ->constrained('siswa_akademik')
                ->onDelete('cascade');

            $table->integer('nominal_bulanan');

            $table->date('mulai_bulan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komitmen_infak');
    }
};
