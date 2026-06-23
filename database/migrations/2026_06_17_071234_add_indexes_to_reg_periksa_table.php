<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reg_periksa', function (Blueprint $table) {
            $table->index('tgl_registrasi', 'reg_periksa_tgl_registrasi_index');
            $table->index(['tgl_registrasi', 'status_lanjut'], 'reg_periksa_tgl_status_index');
            $table->index(['tgl_registrasi', 'jam_reg'], 'reg_periksa_tgl_jam_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reg_periksa', function (Blueprint $table) {
            $table->dropIndex('reg_periksa_tgl_registrasi_index');
            $table->dropIndex('reg_periksa_tgl_status_index');
            $table->dropIndex('reg_periksa_tgl_jam_index');
        });
    }
};
