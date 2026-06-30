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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('id');
            $table->foreignId('role_id')->nullable()->after('username')->constrained('roles')->nullOnDelete();
            $table->string('kd_dokter', 20)->nullable()->after('role_id');
            $table->boolean('is_active')->default(true)->after('kd_dokter');
            
            // Membuat email dan password nullable untuk mendukung user dari AD/LDAP
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['username', 'role_id', 'kd_dokter', 'is_active']);
            
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
