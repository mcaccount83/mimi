<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCountsToCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            // Add increments to the thread and post counts on the categories table
            $table->integer('post_count')->after('enable_threads')->default(0);
            $table->integer('thread_count')->after('enable_threads')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->dropColumn(['thread_count', 'post_count']);
        });
    }
}
