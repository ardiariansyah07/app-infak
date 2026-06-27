<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('sumber')->default('manual')->after('bukti_transfer');
            $table->string('metode_pembayaran')->nullable()->after('sumber');
        });

        DB::table('pembayaran')
            ->where('bukti_transfer', 'import-saldo-awal')
            ->update([
                'sumber' => 'import_saldo_awal',
                'metode_pembayaran' => 'saldo_awal',
                'bukti_transfer' => null,
            ]);

        DB::table('pembayaran')
            ->where('bukti_transfer', 'import-tagihan-awal')
            ->update([
                'sumber' => 'import_tagihan_awal',
                'metode_pembayaran' => 'saldo_awal',
                'bukti_transfer' => null,
            ]);

        DB::table('pembayaran')
            ->whereNotNull('bukti_transfer')
            ->update(['sumber' => 'unggahan_lama', 'metode_pembayaran' => 'transfer']);
    }

    public function down(): void
    {
        DB::table('pembayaran')
            ->where('sumber', 'import_saldo_awal')
            ->update(['bukti_transfer' => 'import-saldo-awal']);

        DB::table('pembayaran')
            ->where('sumber', 'import_tagihan_awal')
            ->update(['bukti_transfer' => 'import-tagihan-awal']);

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['sumber', 'metode_pembayaran']);
        });
    }
};
