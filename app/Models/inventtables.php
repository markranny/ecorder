<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventtables extends Model
{
    use HasFactory;

    protected $table = 'inventtables';
    protected $primaryKey = 'itemid';
    public $timestamps = true; 

    protected $fillable = [
        'itemid',
        'itemgroupid',
        'itemname',
        'itemtype',
        'namealias',
        'notes'
    ];
}
