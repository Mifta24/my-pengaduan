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
        Schema::table('announcements', function (Blueprint $table) {
            // Add new columns first
            $table->string('slug')->unique()->after('title');
            $table->text('summary')->nullable()->after('slug');
            $table->enum('priority', ['urgent', 'high', 'medium', 'low'])->default('medium')->after('content');
            $table->json('target_audience')->nullable()->after('priority');
            $table->json('attachments')->nullable()->after('target_audience');
            $table->boolean('is_active')->default(true)->after('attachments');
            $table->boolean('is_sticky')->default(false)->after('is_active');
            $table->boolean('allow_comments')->default(true)->after('is_sticky');
            $table->timestamp('published_at')->nullable()->after('allow_comments');
            $table->unsignedInteger('views_count')->default(0)->after('published_at');
            $table->softDeletes();
        });

        // Update existing data
        DB::statement('UPDATE announcements SET slug = LOWER(REPLACE(title, " ", "-")) WHERE slug IS NULL');
        DB::statement('UPDATE announcements SET published_at = created_at WHERE published_at IS NULL');

        Schema::table('announcements', function (Blueprint $table) {
            // Rename created_by to author_id
            $table->renameColumn('created_by', 'author_id');
            // Drop date column as we're using published_at instead
            $table->dropColumn('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Restore old structure
            $table->renameColumn('author_id', 'created_by');
            $table->date('date')->after('content');

            // Drop new columns
            $table->dropColumn([
                'slug', 'summary', 'priority', 'target_audience', 'attachments',
                'is_active', 'is_sticky', 'allow_comments', 'published_at',
                'views_count'
            ]);
            $table->dropSoftDeletes();
        });
    }
};
