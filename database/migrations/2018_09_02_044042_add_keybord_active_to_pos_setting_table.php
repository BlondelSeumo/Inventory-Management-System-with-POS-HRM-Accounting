<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeybordActiveToPosSettingTable extends Migration
{
    public function up()
    {
        Schema::table('pos_setting', function (Blueprint $table) {
            $table->boolean('keybord_active')->after('product_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_setting', function (Blueprint $table) {
            //
        });
    }
}
