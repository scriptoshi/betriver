<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game_market', function (Blueprint $table) {
            //
            $table->string('uuid')
                ->after('winning_bet_id')
                ->nullable()
                ->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_market', function (Blueprint $table) {
            //
            $table->dropColumn(['uuid']);
        });
    }
};
