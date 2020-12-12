<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockCount extends Model
{
    protected $table = 'stock_counts';
    protected $fillable =[
        "reference_no", "warehouse_id", "brand_id", "category_id", "user_id", "type", "initial_file", "final_file", "note", "is_adjusted"
    ];
}
