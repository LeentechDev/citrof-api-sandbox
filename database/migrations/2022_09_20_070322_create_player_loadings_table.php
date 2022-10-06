<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerLoadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_loadings', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no');
            $table->string('player_id');
            $table->decimal('amount', 8,2);
            $table->decimal('previous_credits', 8,2);
            $table->decimal('current_credits', 8,2);
            $table->string('type')->comment('1-CASHIN, 2-CASHOUT');
            $table->string('description');
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
        Schema::dropIfExists('player_loadings');
    }
}
