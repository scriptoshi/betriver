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
        Schema::create('transactions', function (Blueprint $table) { 
			$table->bigIncrements('id');
			$table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
			$table->morphs('transactable');
			$table->uuid('uuid')->unique();
			$table->string('description')->nullable();
			$table->decimal('amount',10,2);
			$table->decimal('balance_before',10,2);
			$table->string('action')->default('credit');
			$table->string('type')->default('withdraw');
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
        Schema::drop('transactions');
    }
};
