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
        $driver = DB::getDriverName();

        // Normalize legacy status values before adding stricter guards.
        DB::table('complaints')
            ->where('status', 'completed')
            ->update(['status' => 'resolved']);

        $this->enforceCommentsAnnouncementRequirement($driver);
        $this->enforceComplaintStatusConsistency($driver);
        $this->migrateLegacyComplaintResponseColumns();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::unprepared('DROP TRIGGER IF EXISTS comments_announcement_required_insert');
            DB::unprepared('DROP TRIGGER IF EXISTS comments_announcement_required_update');
            DB::unprepared('DROP TRIGGER IF EXISTS complaints_status_allowed_insert');
            DB::unprepared('DROP TRIGGER IF EXISTS complaints_status_allowed_update');
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE complaints DROP CONSTRAINT IF EXISTS complaints_status_allowed_check');
        }

        $hasResponse = Schema::hasColumn('complaints', 'response');
        $hasAdminResponse = Schema::hasColumn('complaints', 'admin_response');

        Schema::table('complaints', function (Blueprint $table) use ($hasResponse, $hasAdminResponse) {
            if (!$hasResponse) {
                $table->text('response')->nullable();
            }

            if (!$hasAdminResponse) {
                $table->text('admin_response')->nullable();
            }
        });
    }

    private function enforceCommentsAnnouncementRequirement(string $driver): void
    {
        if (!Schema::hasColumn('comments', 'announcement_id')) {
            return;
        }

        DB::table('comments')->whereNull('announcement_id')->delete();

        if ($driver === 'sqlite') {
            DB::unprepared('DROP TRIGGER IF EXISTS comments_announcement_required_insert');
            DB::unprepared('DROP TRIGGER IF EXISTS comments_announcement_required_update');

            DB::unprepared("CREATE TRIGGER comments_announcement_required_insert
                BEFORE INSERT ON comments
                FOR EACH ROW
                WHEN NEW.announcement_id IS NULL
                BEGIN
                    SELECT RAISE(ABORT, 'comments.announcement_id is required');
                END;");

            DB::unprepared("CREATE TRIGGER comments_announcement_required_update
                BEFORE UPDATE OF announcement_id ON comments
                FOR EACH ROW
                WHEN NEW.announcement_id IS NULL
                BEGIN
                    SELECT RAISE(ABORT, 'comments.announcement_id is required');
                END;");

            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE comments MODIFY announcement_id BIGINT UNSIGNED NOT NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE comments ALTER COLUMN announcement_id SET NOT NULL');
        }
    }

    private function enforceComplaintStatusConsistency(string $driver): void
    {
        $allowed = "'pending','in_progress','waiting_user_confirmation','resolved','rejected'";

        if ($driver === 'sqlite') {
            DB::unprepared('DROP TRIGGER IF EXISTS complaints_status_allowed_insert');
            DB::unprepared('DROP TRIGGER IF EXISTS complaints_status_allowed_update');

            DB::unprepared("CREATE TRIGGER complaints_status_allowed_insert
                BEFORE INSERT ON complaints
                FOR EACH ROW
                WHEN NEW.status NOT IN ({$allowed})
                BEGIN
                    SELECT RAISE(ABORT, 'invalid complaints.status value');
                END;");

            DB::unprepared("CREATE TRIGGER complaints_status_allowed_update
                BEFORE UPDATE OF status ON complaints
                FOR EACH ROW
                WHEN NEW.status NOT IN ({$allowed})
                BEGIN
                    SELECT RAISE(ABORT, 'invalid complaints.status value');
                END;");

            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE complaints MODIFY status ENUM({$allowed}) NOT NULL DEFAULT 'pending'");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE complaints DROP CONSTRAINT IF EXISTS complaints_status_allowed_check');
            DB::statement("ALTER TABLE complaints ADD CONSTRAINT complaints_status_allowed_check CHECK (status IN ({$allowed}))");
        }
    }

    private function migrateLegacyComplaintResponseColumns(): void
    {
        $hasLegacyResponse = Schema::hasColumn('complaints', 'response');
        $hasLegacyAdminResponse = Schema::hasColumn('complaints', 'admin_response');

        if (!$hasLegacyResponse && !$hasLegacyAdminResponse) {
            return;
        }

        if (!Schema::hasTable('responses')) {
            return;
        }

        $adminUserId = DB::table('users')
            ->whereIn('role', ['admin', 'superadmin'])
            ->value('id');

        $selectColumns = ['id', 'user_id', 'created_at', 'updated_at'];

        if ($hasLegacyResponse) {
            $selectColumns[] = 'response';
        }

        if ($hasLegacyAdminResponse) {
            $selectColumns[] = 'admin_response';
        }

        $complaints = DB::table('complaints')->select($selectColumns)->get();

        foreach ($complaints as $complaint) {
            $messages = [];

            if ($hasLegacyAdminResponse) {
                $adminResponse = trim((string) ($complaint->admin_response ?? ''));
                if ($adminResponse !== '') {
                    $messages[] = $adminResponse;
                }
            }

            if ($hasLegacyResponse) {
                $legacyResponse = trim((string) ($complaint->response ?? ''));
                if ($legacyResponse !== '' && !in_array($legacyResponse, $messages, true)) {
                    $messages[] = $legacyResponse;
                }
            }

            foreach ($messages as $message) {
                DB::table('responses')->insert([
                    'complaint_id' => $complaint->id,
                    'user_id' => $adminUserId ?: $complaint->user_id,
                    'content' => $message,
                    'photo' => null,
                    'created_at' => $complaint->updated_at ?? $complaint->created_at ?? now(),
                    'updated_at' => $complaint->updated_at ?? $complaint->created_at ?? now(),
                ]);
            }
        }

        Schema::table('complaints', function (Blueprint $table) use ($hasLegacyResponse, $hasLegacyAdminResponse) {
            if ($hasLegacyResponse && $hasLegacyAdminResponse) {
                $table->dropColumn(['response', 'admin_response']);
                return;
            }

            if ($hasLegacyResponse) {
                $table->dropColumn('response');
            }

            if ($hasLegacyAdminResponse) {
                $table->dropColumn('admin_response');
            }
        });
    }
};
