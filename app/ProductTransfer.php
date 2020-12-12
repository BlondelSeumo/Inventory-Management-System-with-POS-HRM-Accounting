<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTransfer extends Model
{
    protected $table = 'product_transfer';
    protected $fillable =[

        "transfer_id", "product_id", "variant_id", "qty", "purchase_unit_id", "net_unit_cost", "tax_rate", "tax", "total"
    ];
}
