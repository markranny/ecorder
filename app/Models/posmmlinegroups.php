<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class posmmlinegroups extends Model
{
    use HasFactory;
    protected $fillable = [
        'OFFERID',
        'LINEGROUP',
        'NOOFITEMSNEEDED',
        'DESCRIPTION',
    ];
}
