<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkIndices extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->index('category_id');
        });

        Schema::table('forum_posts', function (Blueprint $table) {
            $table->index('thread_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropIndex('forum_threads_category_id_index');
        });

        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropIndex('forum_posts_thread_id_index');
        });
    }
}
