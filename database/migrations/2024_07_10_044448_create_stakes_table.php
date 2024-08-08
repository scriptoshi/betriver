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
        Schema::create('stakes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('slip_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('bet_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('uid')->unique();
            $table->string('scoreType');
            $table->decimal('amount', 10, 2)->default();
            $table->decimal('filled', 10, 2)->default();
            $table->decimal('unfilled', 10, 2)->default();
            $table->decimal('payout', 10, 2)->default();
            $table->decimal('odds', 6, 2)->default();
            $table->string('status');
            $table->boolean('won')->default(false);
            $table->boolean('is_withdrawn')->default(false);
            $table->boolean('allow_partial')->default(true);
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
        Schema::drop('stakes');
    }
};
