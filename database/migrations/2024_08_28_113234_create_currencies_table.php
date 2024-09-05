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
        Schema::create('currencies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('gateway')->nullable();
            $table->string('code')->nullable();
            $table->string('symbol')->nullable();
            $table->string('name')->nullable();
            $table->string('regex')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('chain')->nullable();
            $table->string('contract')->nullable();
            $table->string('explorer')->nullable();
            $table->decimal('rate', 24, 16)->nullable();
            $table->integer('precision')->default(2);
            $table->boolean('active')->default(false);
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
        Schema::drop('currencies');
    }
};
