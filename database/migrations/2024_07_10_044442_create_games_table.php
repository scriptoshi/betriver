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
        Schema::create('games', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');
            $table->foreignId('league_id')->nullable()->constrained();
            $table->foreignId('home_team_id')->nullable()->constrained('teams');
            $table->foreignId('away_team_id')->nullable()->constrained('teams');
            $table->string('name')->nullable();
            $table->string('gameId')->nullable()->unique();
            $table->timestamp('startTime')->nullable();
            $table->timestamp('endTime')->nullable();
            $table->string('status')->nullable();
            $table->string('sport')->default('football');
            $table->boolean('active')->default(true);
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
        Schema::drop('games');
    }
};
