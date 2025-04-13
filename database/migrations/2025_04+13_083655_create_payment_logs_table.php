<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->string('response_code')->nullable();
            $table->text('response_message')->nullable();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_category_subscriptions');
    }
};

