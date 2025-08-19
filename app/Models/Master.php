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

    protected $appends = ['statusLabel'];

    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Aktif' : 'Tidak Aktif';
    }
}
