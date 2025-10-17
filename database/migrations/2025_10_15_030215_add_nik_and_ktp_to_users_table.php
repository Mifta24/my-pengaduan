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
            $table->string('nik', 16)->nullable()->after('email'); //National indetity number
            $table->string('ktp_path')->nullable()->after('nik');
            $table->string('phone', 20)->nullable()->after('address');
            $table->string('rt_number', 3)->nullable()->after('phone');
            $table->string('rw_number', 3)->nullable()->after('rt_number');
            $table->boolean('is_verified')->default(false)->after('rw_number');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik', 'ktp_path', 'phone', 'rt_number', 'rw_number', 'is_verified', 'verified_at']);
        });
    }
};
