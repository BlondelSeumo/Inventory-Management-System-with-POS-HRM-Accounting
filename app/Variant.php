<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $fillable = ['name'];

    public function product()
    {
    	return $this->belongsToMany('App\Variant', 'product_variants');
    }
}
