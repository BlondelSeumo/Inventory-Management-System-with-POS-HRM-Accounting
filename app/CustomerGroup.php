<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $fillable =[

        "name", "percentage", "is_active"
    ];
}
