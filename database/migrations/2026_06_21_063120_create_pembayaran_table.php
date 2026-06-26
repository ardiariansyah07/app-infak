<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->onDelete('cascade');

            $table->date('tanggal');

            $table->integer('nominal');

            $table->string('bukti_transfer')->nullable();

            $table->enum('status_verifikasi', ['pending', 'valid', 'ditolak'])
                ->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
