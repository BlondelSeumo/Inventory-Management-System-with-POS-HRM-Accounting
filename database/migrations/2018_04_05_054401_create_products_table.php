<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code');
            $table->string('type');
            $table->string('barcode_symbology');
            $table->integer('brand_id')->nullable();
            $table->integer('category_id');
            $table->integer('unit_id');
            $table->integer('purchase_unit_id');
            $table->integer('sale_unit_id');
            $table->string('cost');
            $table->string('price');
            $table->double('qty')->nullable();
            $table->double('alert_quantity')->nullable();
            $table->tinyInteger('promotion')->nullable();
            $table->string('promotion_price')->nullable();
            $table->date('starting_date')->nullable();
            $table->date('last_date')->nullable();
            $table->integer('tax_id')->nullable();
            $table->integer('tax_method')->nullable();
            $table->longText('image')->nullable();
            $table->tinyInteger('featured')->nullable();
            $table->text('product_details')->nullable();
            $table->boolean('is_active')->nullable();
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
        Schema::dropIfExists('products');
    }
}
