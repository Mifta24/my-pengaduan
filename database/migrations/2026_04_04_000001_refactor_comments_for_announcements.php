<?php

use App\Models\Announcement;
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
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('announcement_id')
                ->nullable()
                ->after('user_id')
                ->constrained('announcements')
                ->cascadeOnDelete();
        });

        // Migrate existing announcement comments from polymorphic columns.
        DB::table('comments')
            ->where('commentable_type', Announcement::class)
            ->update(['announcement_id' => DB::raw('commentable_id')]);

        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'commentable_id') && Schema::hasColumn('comments', 'commentable_type')) {
                $table->dropMorphs('commentable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->morphs('commentable');
        });

        DB::table('comments')
            ->whereNotNull('announcement_id')
            ->update([
                'commentable_id' => DB::raw('announcement_id'),
                'commentable_type' => Announcement::class,
            ]);

        Schema::table('comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('announcement_id');
        });
    }
};
