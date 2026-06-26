<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            if (! Schema::hasColumn('siswa', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('rombel', function (Blueprint $table) {
            if (! Schema::hasColumn('rombel', 'tingkat')) {
                $table->enum('tingkat', ['X', 'XI', 'XII'])
                    ->nullable()
                    ->after('nama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rombel', function (Blueprint $table) {
            if (Schema::hasColumn('rombel', 'tingkat')) {
                $table->dropColumn('tingkat');
            }
        });

        Schema::table('siswa', function (Blueprint $table) {
            if (Schema::hasColumn('siswa', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
