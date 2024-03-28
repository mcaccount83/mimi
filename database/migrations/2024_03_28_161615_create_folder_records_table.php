<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('folder_records', function (Blueprint $table) {
            $table->id();
            $table->string('state');
            $table->string('chapter_name')->nullable(); // Make the chapter_name field nullable
            $table->string('folder_id');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folder_records');
    }
};
