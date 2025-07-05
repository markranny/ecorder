<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'ACCOUNTNUM',
        'NAME',
        'ADDRESS',
        'PHONE',
        'CURRENCY',
        'BLOCKED',
        'CREDITMAX',
        'COUNTRY',
        'ZIPCODE',
        'STATE',
        /* 'COUNTY', */
        /* 'URL', */
        'EMAIL',
        'CELLULARPHONE',
        /* 'DATAAREAID', */
        'GENDER',
    ];
}

