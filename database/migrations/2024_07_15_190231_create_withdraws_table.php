<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('uuid')->unique();
            $table->string('gateway')->default('Wallet');
            $table->string('remoteId')->nullable();
            $table->string('batchId')->nullable();
            $table->string('to')->nullable();
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('fees', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->string('currency');
            $table->decimal('gateway_amount', 24, 16)->nullable();
            $table->string('gateway_currency');
            $table->json('data')->nullable();
            $table->string('status')->default();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('withdraws');
    }
};
