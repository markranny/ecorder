<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rbostoretables extends Model
{
    use HasFactory;

    protected $fillable = [
        'STOREID',
        'NAME',
        'ROUTES',
        'TYPES',
        'BLOCKED',
        'ADDRESS',
        'STREET',
        'ZIPCODE',
        'CITY',
        'STATE',
        'COUNTRY',
        'PHONE',
        'CURRENCY',
        'SQLSERVERNAME',
        'DATABASENAME',
        'USERNAME',
        'PASSWORD',
        'WINDOWSAUTHENTICATION',
        'FORMINFOFIELD1',
        'FORMINFOFIELD2',
        'FORMINFOFIELD3',
        'FORMINFOFIELD4',
        'ROUTES',
        'TYPES',
        'BLOCKED',
        'CUTOFFTIME'
    ];

    public $timestamps = true;
}
