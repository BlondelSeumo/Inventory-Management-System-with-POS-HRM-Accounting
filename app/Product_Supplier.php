<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_Supplier extends Model
{
	protected $table = 'product_supplier';
    protected $fillable =[

        "product_code", "supplier_id", "qty", "price"
    ];
}
