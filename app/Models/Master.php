<?php

namespace App\Models;

use Btx\Query\Model;

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

    protected $appends = ['statusLabel'];

    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Aktif' : 'Tidak Aktif';
    }
}
