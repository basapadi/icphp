<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends BaseModel
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'label',
        'status',
        'data'
    ];

    protected $casts = [
        'data' => 'object',
    ];
}
