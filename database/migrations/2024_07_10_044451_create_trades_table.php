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
        Schema::create('trades', function (Blueprint $table) { 
			$table->bigIncrements('id');
			$table->foreignId('maker_id')->constrained('stakes')->onUpdate('cascade')->onDelete('cascade');
			$table->foreignId('taker_id')->constrained('stakes')->onUpdate('cascade')->onDelete('cascade');
			$table->decimal('amount',10,2)->default();
			$table->decimal('buy',10,2)->default();
			$table->decimal('sell',10,2)->default();
			$table->decimal('margin',10,2)->default();
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
        Schema::drop('trades');
    }
};
