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
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->renameColumn('parent_category', 'category_id');
            $table->renameColumn('subtitle', 'description');
        });

        Schema::table('forum_categories', function (Blueprint $table) {
            $table->integer('category_id')->default(0)->change();
            $table->string('description')->nullable()->change();
            $table->integer('weight')->default(0)->change();

            $table->boolean('enable_threads')->default(0);
            $table->boolean('private')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->renameColumn('category_id', 'parent_category');
            $table->dropColumn(['created_at', 'updated_at', 'enable_threads', 'private']);
            $table->renameColumn('description', 'subtitle');
        });
    }
};
