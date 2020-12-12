<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference_no');
            $table->integer('customer_id');
            $table->integer('warehouse_id');
            $table->integer('biller_id');
            $table->integer('item');
            $table->double('total_qty');
            $table->double('total_discount');
            $table->double('total_tax');
            $table->double('total_price');
            $table->double('grand_total');
            $table->double('order_tax_rate')->nullable();
            $table->double('order_tax')->nullable();
            $table->double('order_discount')->nullable();
            $table->double('shipping_cost')->nullable();
            $table->integer('sale_status');
            $table->integer('payment_status');
            $table->string('document')->nullable();
            $table->double('paid_amount')->nullable();
            $table->text('sale_note')->nullable();
            $table->text('staff_note')->nullable();
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
        Schema::dropIfExists('sales');
    }
}
