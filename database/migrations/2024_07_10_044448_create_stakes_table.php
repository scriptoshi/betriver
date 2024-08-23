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
        Schema::create('stakes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('bet_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('market_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('original_stake_id')->nullable()->constrained('stakes');
            $table->string('uid')->unique();
            $table->string('scoreType');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('filled', 10, 2)->default(0);
            $table->decimal('unfilled', 10, 2)->default(0);
            $table->decimal('qty', 10, 2)->default(0);
            $table->decimal('payout', 10, 2)->default(0);
            $table->decimal('liability', 10, 2)->default(0);
            $table->decimal('profit_loss', 10, 2)->default(0);
            $table->decimal('odds', 6, 2)->default();
            $table->string('type');
            $table->string('status')->default('pending');
            $table->boolean('won')->default(false);
            $table->boolean('is_withdrawn')->default(false);
            $table->boolean('allow_partial')->default(true);
            $table->boolean('is_trade_out')->default(false);
            $table->boolean('is_traded_out')->default(false);
            $table->timestamps();
            $table->softDeletes();
            // ensure positive decimal values
            $table->check('filled > 0', 'filled_positive');
            $table->check('unfilled > 0', 'unfilled_positive');
            $table->check('amount > 0', 'amount_positive');
            $table->check('payout > 0', 'payout_positive');
            $table->check('liability > 0', 'liability_positive');
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
