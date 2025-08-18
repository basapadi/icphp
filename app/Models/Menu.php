<?php

namespace App\Models;

use Btx\Query\Model;

class Menu extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'icon',
        'label',
        'route',
        'order',
        'parent_id'
    ];

    protected $appends = ['active'];

    public function getActiveAttribute()
    {
        return request()->route == $this->route;
    }

    public function subItems(){
        return $this->hasMany($this,'parent_id','id');
    }

    public function parent(){
        return $this->belongsTo($this,'id','parent_id');
    }
}
