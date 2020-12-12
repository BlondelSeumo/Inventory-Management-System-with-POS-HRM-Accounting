<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_setting', function (Blueprint $table) {
            $table->integer('id')->unique();
            $table->integer('customer_id');
            $table->integer('warehouse_id');
            $table->integer('biller_id');
            $table->integer('product_number');
            $table->string('stripe_public_key')->nullable();
            $table->string('stripe_secret_key');
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
        Schema::dropIfExists('pos_setting');
    }
}
