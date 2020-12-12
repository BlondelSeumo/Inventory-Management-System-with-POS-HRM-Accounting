<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable =[

        "purchase_id", "user_id", "sale_id", "cash_register_id", "account_id", "payment_reference", "amount", "change", "paying_method", "payment_note"
    ];
}
