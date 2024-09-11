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
        Schema::create('wagers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('ticket_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('bet_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('odd_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('sport');
            $table->string('game_info')->nullable();
            $table->string('bet_info')->nullable();
            $table->string('market_info')->nullable();
            $table->string('status')
                ->default('pending')
                ->nullable();
            $table->decimal('odds', 6, 2)->default();
            $table->boolean('won')->default(false);
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
        Schema::drop('wagers');
    }
};
