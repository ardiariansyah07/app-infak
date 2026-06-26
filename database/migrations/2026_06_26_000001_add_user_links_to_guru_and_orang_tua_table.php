<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            if (! Schema::hasColumn('guru', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('orang_tua', function (Blueprint $table) {
            if (! Schema::hasColumn('orang_tua', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orang_tua', function (Blueprint $table) {
            if (Schema::hasColumn('orang_tua', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });

        Schema::table('guru', function (Blueprint $table) {
            if (Schema::hasColumn('guru', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
