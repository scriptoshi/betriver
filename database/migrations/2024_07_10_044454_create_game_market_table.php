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
        Schema::create('game_market', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('game_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('market_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('winning_bet_id')->nullable()->constrained('bets');
            $table->boolean('active')->default(true);
            $table->boolean('bookie_active')->default(true);
            $table->unique(['game_id', 'market_id']);
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
        Schema::drop('game_market');
    }
};
