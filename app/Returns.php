<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{
	protected $table = 'returns';
    protected $fillable =[
        "reference_no", "user_id", "cash_register_id", "customer_id", "warehouse_id", "biller_id", "account_id", "item", "total_qty", "total_discount", "total_tax", "total_price","order_tax_rate", "order_tax", "grand_total", "document", "return_note", "staff_note"
    ];

    public function biller()
    {
    	return $this->belongsTo('App\Biller');
    }

    public function customer()
    {
    	return $this->belongsTo('App\Customer');
    }

    public function warehouse()
    {
    	return $this->belongsTo('App\Warehouse');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
