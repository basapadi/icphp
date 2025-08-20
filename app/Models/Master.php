<?php

namespace App\Models;
class Master extends BaseModel
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
