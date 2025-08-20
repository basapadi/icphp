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

    protected $appends = ['status_label'];

    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Aktif' : 'Tidak Aktif';
    }
}
