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
        Schema::create('deposits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('uuid')->unique();
            $table->string('batch_id')->nullable();
            $table->string('gateway')->default('Wallet');
            $table->string('remoteId')->nullable();
            $table->string('deposit_address')->nullable();
            $table->string('from')->nullable();
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('fees', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->string('amount_currency');
            $table->decimal('gateway_amount', 10, 2)->nullable();
            $table->string('gateway_currency');
            $table->json('data')->nullable();
            $table->string('gateway_error', 3000)->nullable();
            $table->string('status')->default();
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
        Schema::drop('deposits');
    }
};
