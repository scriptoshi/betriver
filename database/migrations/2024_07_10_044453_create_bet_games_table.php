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
        Schema::create('bet_games', function (Blueprint $table) { 
			$table->bigIncrements('id');
			$table->foreignId('bet_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
			$table->foreignId('game_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
			$table->unique(['bet_id','game_id']);
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
        Schema::drop('bet_games');
    }
};
