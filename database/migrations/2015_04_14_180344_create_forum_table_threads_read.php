<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forum_threads_read', function (Blueprint $table) {
            $table->integer('thread_id')->unsigned();
            $table->foreignIdFor(config('forum.integration.user_model'), 'user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('forum_threads_read');
    }
};
