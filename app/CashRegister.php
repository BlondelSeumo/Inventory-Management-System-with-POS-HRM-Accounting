<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    protected $fillable = ["cash_in_hand", "user_id", "warehouse_id", "status"];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function warehouse()
    {
    	return $this->belongsTo('App\Warehouse');
    }
}
