<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftCardRechargesTable extends Migration
{
    public function up()
    {
        Schema::create('gift_card_recharges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gift_card_id');
            $table->double('amount');
            $table->integer('user_id');
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
        Schema::dropIfExists('gift_card_recharges');
    }
}
