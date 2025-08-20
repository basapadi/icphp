<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Contact extends BaseModel
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'type',
        'nama',
        'alamat',
        'telepon',
        'email',
        'status'
    ];
}
