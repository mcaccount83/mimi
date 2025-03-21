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
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->integer('newest_thread_id')->after('accepts_threads')->unsigned()->nullable();
            $table->integer('latest_active_thread_id')->after('newest_thread_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->dropColumn('newest_thread_id');
            $table->dropColumn('latest_active_thread_id');
        });
    }
};
