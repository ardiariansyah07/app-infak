<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alokasi_pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pembayaran_id')
                ->constrained('pembayaran')
                ->onDelete('cascade');

            $table->foreignId('tagihan_infak_id')
                ->constrained('tagihan_infak')
                ->onDelete('cascade');

            $table->integer('nominal');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alokasi_pembayaran');
    }
};
