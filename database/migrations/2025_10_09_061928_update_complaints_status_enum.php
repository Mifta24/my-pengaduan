<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the status enum to match controller expectations
        Schema::table('complaints', function (Blueprint $table) {
            // First change existing data to new values
            DB::table('complaints')->where('status', 'processing')->update(['status' => 'in_progress']);
            DB::table('complaints')->where('status', 'completed')->update(['status' => 'resolved']);

            // Drop the old enum column and recreate with new values
            $table->dropColumn('status');
        });

        Schema::table('complaints', function (Blueprint $table) {
            // Add the new status column with updated enum values
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'rejected'])->default('pending')->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Revert data to old values
            DB::table('complaints')->where('status', 'in_progress')->update(['status' => 'processing']);
            DB::table('complaints')->where('status', 'resolved')->update(['status' => 'completed']);

            // Drop the new enum column
            $table->dropColumn('status');
        });

        Schema::table('complaints', function (Blueprint $table) {
            // Restore the old status column
            $table->enum('status', ['pending', 'processing', 'completed'])->default('pending')->after('location');
        });
    }
};
