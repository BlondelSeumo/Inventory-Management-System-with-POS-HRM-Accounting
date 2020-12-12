<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
     protected $fillable =[
        "card_no", "amount", "expense", "customer_id", "user_id", "expired_date", "created_by", "is_active"  
    ];
}
