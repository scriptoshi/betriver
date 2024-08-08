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
        Schema::create('scores', function (Blueprint $table) { 
			$table->bigIncrements('id');
			$table->foreignId('game_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
			$table->string('type')->default('goals');
			$table->string('home',600);
			$table->string('away',600);
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
        Schema::drop('scores');
    }
};
