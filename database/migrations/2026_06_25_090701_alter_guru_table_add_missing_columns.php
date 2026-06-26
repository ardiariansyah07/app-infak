<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru', function (Blueprint $table) {

            if (! Schema::hasColumn('guru', 'jenis_kelamin')) {

                $table->enum('jenis_kelamin', ['L', 'P'])
                    ->after('nama');

            }

            if (! Schema::hasColumn('guru', 'no_hp')) {

                $table->string('no_hp', 20)
                    ->nullable()
                    ->after('jenis_kelamin');

            }

            if (! Schema::hasColumn('guru', 'alamat')) {

                $table->text('alamat')
                    ->nullable()
                    ->after('email');

            }

            if (! Schema::hasColumn('guru', 'aktif')) {

                $table->boolean('aktif')
                    ->default(true)
                    ->after('alamat');

            }

        });
    }

    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {

            if (Schema::hasColumn('guru', 'aktif')) {
                $table->dropColumn('aktif');
            }

            if (Schema::hasColumn('guru', 'alamat')) {
                $table->dropColumn('alamat');
            }

            if (Schema::hasColumn('guru', 'no_hp')) {
                $table->dropColumn('no_hp');
            }

            if (Schema::hasColumn('guru', 'jenis_kelamin')) {
                $table->dropColumn('jenis_kelamin');
            }

        });
    }
};
