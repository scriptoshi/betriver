<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

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
        Schema::create('odds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('bet_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('market_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->morphs('game');
            $table->string('bookie')->nullable();
            $table->string('md5')->unique();
            $table->boolean('active')->default(true);
            $table->decimal('odd', 5, 2)->default();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('odds');
    }
};
