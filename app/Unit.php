<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable =[

        "unit_code", "unit_name", "base_unit", "operator", "operation_value", "is_active"
    ];

    public function product()
    {
    	return $this->hasMany('App/Product');
    	
    }
}
