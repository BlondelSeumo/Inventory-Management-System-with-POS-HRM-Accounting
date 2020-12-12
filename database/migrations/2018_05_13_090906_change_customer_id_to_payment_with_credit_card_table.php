<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCustomerIdToPaymentWithCreditCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_with_credit_card', function (Blueprint $table) {
            $table->integer('customer_id')->nullable()->change();
            $table->string('customer_stripe_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_with_credit_card', function (Blueprint $table) {
            //
        });
    }
}
