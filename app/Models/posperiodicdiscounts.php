<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class posperiodicdiscounts extends Model
{
    use HasFactory;
    protected $fillable = [
        'OFFERID',
        'DESCRIPTION',
        'STATUS',
        'PDTYPE',
        'PRIORITY',
        'DISCVALIDPERIODID',
        'DISCOUNTTYPE',
        'NOOFLINESTOTRIGGER',
        'DEALPRICEVALUE',
        'DISCOUNTPCTVALUE',
        'DISCOUNTAMOUNTVALUE',
        'PRICEGROUP',
        'TRIGGERED',
    ];
}
