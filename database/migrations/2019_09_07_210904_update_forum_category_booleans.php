<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForumCategoryBooleans extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->renameColumn('enable_threads', 'accepts_threads');
            $table->renameColumn('private', 'is_private');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->renameColumn('accepts_threads', 'enable_threads');
            $table->renameColumn('is_private', 'private');
        });
    }
}
