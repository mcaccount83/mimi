<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDefaultsToForumTableThreadsColumns extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->boolean('pinned')->nullable()->default(0)->change();
            $table->boolean('locked')->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->boolean('pinned')->nullable(false)->default(null)->change();
            $table->boolean('locked')->nullable(false)->default(null)->change();
        });
    }
}
