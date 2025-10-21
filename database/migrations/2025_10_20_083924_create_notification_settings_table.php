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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('complaint_created')->default(true);
            $table->boolean('complaint_status_changed')->default(true);
            $table->boolean('announcement_created')->default(true);
            $table->boolean('admin_response')->default(true);
            $table->boolean('comment_added')->default(true);
            $table->boolean('push_enabled')->default(true);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
