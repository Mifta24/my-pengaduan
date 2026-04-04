<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->timestamp('admin_resolved_at')->nullable()->after('admin_response');
            $table->timestamp('user_resolved_at')->nullable()->after('admin_resolved_at');
            $table->timestamp('auto_resolve_at')->nullable()->after('user_resolved_at');
            $table->timestamp('resolved_at')->nullable()->after('auto_resolve_at');
            $table->string('resolved_by', 20)->nullable()->after('resolved_at');

            $table->index('auto_resolve_at');
        });

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE complaints MODIFY status ENUM('pending','in_progress','waiting_user_confirmation','resolved','rejected') NOT NULL DEFAULT 'pending'");
        }

        DB::table('complaints')
            ->where('status', 'resolved')
            ->whereNull('resolved_at')
            ->update([
                'resolved_at' => DB::raw('updated_at'),
                'resolved_by' => 'admin',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        DB::table('complaints')
            ->where('status', 'waiting_user_confirmation')
            ->update(['status' => 'in_progress']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE complaints MODIFY status ENUM('pending','in_progress','resolved','rejected') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropIndex(['auto_resolve_at']);
            $table->dropColumn([
                'admin_resolved_at',
                'user_resolved_at',
                'auto_resolve_at',
                'resolved_at',
                'resolved_by',
            ]);
        });
    }
};
