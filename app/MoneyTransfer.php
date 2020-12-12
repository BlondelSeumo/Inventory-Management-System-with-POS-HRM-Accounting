<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoneyTransfer extends Model
{
    protected $fillable = ['reference_no', 'from_account_id', 'to_account_id', 'amount'];

    public function fromAccount()
    {
    	return $this->belongsTo('App\Account');
    }

    public function toAccount()
    {
    	return $this->belongsTo('App\Account');
    }
}
