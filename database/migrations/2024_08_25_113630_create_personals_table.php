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
        Schema::create('personals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('proof_of_identity')->nullable();
            $table->string('proof_of_identity_type')->nullable();
            $table->string('proof_of_address')->nullable();
            $table->string('proof_of_address_type')->nullable();
            $table->string('bet_emails')->nullable()->default('summary');
            $table->boolean('mailing_list')->default(true);
            $table->boolean('confirm_bets')->default(true);
            $table->decimal('daily_gross_deposit', 10, 2)->nullable();
            $table->decimal('weekly_gross_deposit', 10, 2)->nullable();
            $table->decimal('monthly_gross_deposit', 10, 2)->nullable();
            $table->string('loss_limit_interval')->nullable();
            $table->decimal('loss_limit', 10, 2)->nullable();
            $table->decimal('stake_limit', 10, 2)->nullable();
            $table->timestamp('time_out_at')->nullable();
            $table->timestamp('stake_limit_at')->nullable();
            $table->timestamp('dob')->nullable();
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
        Schema::drop('personals');
    }
};
