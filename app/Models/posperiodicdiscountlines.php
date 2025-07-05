<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class posperiodicdiscountlines extends Model
{
    use HasFactory;
    protected $fillable = [
        'OFFERID',
        'LINEID',
        'PRODUCTTYPE',
        'ID',
        'DEALPRICEORDISCPCT',
        'LINEGROUP',
        'DISCTYPE',
    ];
}
