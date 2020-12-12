<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductQuotation extends Model
{
    protected $table = 'product_quotation';
    protected $fillable =[

        "quotation_id", "product_id", "variant_id", "qty", "sale_unit_id", "net_unit_price", "discount", "tax_rate", "tax", "total"
    ];
}
