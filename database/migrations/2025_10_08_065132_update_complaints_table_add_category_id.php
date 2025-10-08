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
        Schema::table('complaints', function (Blueprint $table) {
            // Drop existing category column
            $table->dropColumn('category');

            // Add foreign key to categories table
            $table->foreignId('category_id')->after('description')->constrained()->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Drop foreign key and add back category column
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->string('category')->after('description');
        });
    }
};
