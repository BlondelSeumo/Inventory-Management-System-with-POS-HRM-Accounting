<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_counts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference_no');
            $table->integer('warehouse_id');
            $table->string('category_id')->nullable();
            $table->string('brand_id')->nullable();
            $table->integer('user_id');
            $table->string('type');
            $table->string('initial_file')->nullable();
            $table->string('final_file')->nullable();
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
        Schema::dropIfExists('stock_counts');
    }
}
