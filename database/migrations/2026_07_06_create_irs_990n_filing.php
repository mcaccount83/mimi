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
        // migration: create_irs_990n_filings_table
        Schema::create('irs_990n_filings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->string('ein', 9);
            $table->string('tax_year', 4);
            $table->date('tax_period_begin')->nullable();
            $table->date('tax_period_end')->nullable();
            $table->string('organization_name')->nullable(); // useful sanity-check if EIN was mistyped
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->index(['chapter_id', 'tax_year']);
        });

    }
};
