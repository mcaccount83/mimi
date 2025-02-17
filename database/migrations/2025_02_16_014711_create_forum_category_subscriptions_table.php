<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_category_subscriptions', function (Blueprint $table) {
            $table->id();
            // Change this line to match your users table id column type
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('category_id');
            $table->timestamps();

            // Add foreign key after defining the column
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Unique constraint to prevent duplicate subscriptions
            $table->unique(['user_id', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_category_subscriptions');
    }
};
