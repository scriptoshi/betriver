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
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('uid')->unique();
            $table->decimal('amount', 10, 2)->default();
            $table->decimal('payout', 10, 2)->default();
            $table->decimal('total_odds', 6, 2)->default();
            $table->string('type');
            $table->string('status')->default('pending');
            $table->boolean('won')->default(false);
            $table->boolean('is_withdrawn')->default(false);
            $table->timestamps();
            $table->softDeletes();
            // ensure positive decimal values
            $table->check('payout > 0', 'tickets_payout_positive');
            $table->check('amount > 0', 'tickets_amount_positive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tickets');
    }
};
