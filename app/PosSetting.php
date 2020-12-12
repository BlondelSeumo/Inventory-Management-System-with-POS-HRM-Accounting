<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PosSetting extends Model
{
    protected $table = 'pos_setting';
    protected $fillable =[
        "customer_id", "warehouse_id", "biller_id", "product_number", "stripe_public_key", "stripe_secret_key", "keybord_active"
    ];
}
