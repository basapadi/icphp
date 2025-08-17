<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Master extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'type',
        'kode',
        'nama',
        'status',
        'attributes',
    ];
}
