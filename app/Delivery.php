<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable =[
        "reference_no", "sale_id", "user_id", "address", "delivered_by", "recieved_by", "file", "status", "note"
    ];

    public function sale()
    {
    	return $this->belongsTo("App\Sale");
    }

    public function user()
    {
    	return $this->belongsTo("App\User");
    }
}
