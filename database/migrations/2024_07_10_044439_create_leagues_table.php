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
        Schema::create('leagues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('leagueId')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->unique();
            $table->string('sport');
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('country')->nullable();
            $table->string('season')->nullable();
            $table->string('race_tag')->nullable();
            $table->timestamp('season_ends_at')->nullable();
            $table->boolean('has_odds')->default(false);
            $table->boolean('active')->default(true);
            $table->boolean('menu')->default(false);
            $table->unique(['leagueId', 'sport']);
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
        Schema::drop('leagues');
    }
};
