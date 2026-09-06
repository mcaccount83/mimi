<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('forum_threads', 'message_id')) {
            Schema::table('forum_threads', function (Blueprint $table) {
                $table->string('message_id')->nullable()->unique()->after('id');
            });
        }

        if (! Schema::hasColumn('forum_posts', 'message_id')) {
            Schema::table('forum_posts', function (Blueprint $table) {
                $table->string('message_id')->nullable()->unique()->after('id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('forum_threads', fn (Blueprint $table) => $table->dropColumn('message_id'));
        Schema::table('forum_posts', fn (Blueprint $table) => $table->dropColumn('message_id'));
    }
};
