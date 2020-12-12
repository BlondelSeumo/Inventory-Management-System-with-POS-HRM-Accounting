<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference_no');
            $table->integer('status');
            $table->integer('from_warehouse_id');
            $table->integer('to_warehouse_id');
            $table->integer('item');
            $table->double('total_qty');
            $table->double('total_tax');
            $table->double('total_cost');
            $table->double('shipping_cost')->nullable();
            $table->double('grand_total');
            $table->string('document')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('transfers');
    }
}
