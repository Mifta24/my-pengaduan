<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Keep comments table strict: each comment must belong to an announcement.
        DB::table('comments')->whereNull('announcement_id')->delete();

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE comments MODIFY announcement_id BIGINT UNSIGNED NOT NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE comments ALTER COLUMN announcement_id SET NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE comments MODIFY announcement_id BIGINT UNSIGNED NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE comments ALTER COLUMN announcement_id DROP NOT NULL');
        }
    }
};
