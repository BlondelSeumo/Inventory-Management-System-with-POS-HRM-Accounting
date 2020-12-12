<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HrmSetting extends Model
{
    protected $fillable =[
        "checkin", "checkout"
    ];
}
