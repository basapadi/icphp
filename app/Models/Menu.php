<?php

namespace App\Models;
class Menu extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'icon',
        'label',
        'route',
        'order',
        'parent_id',
        'side_menu'
    ];

    protected $appends = ['active'];

    public function getActiveAttribute()
    {
        return request()->route == $this->route;
    }

    public function subItems(){
        return $this->hasMany($this,'parent_id','id')->orderBy('order');;
    }

    public function parent(){
       return $this->belongsTo($this, 'parent_id', 'id');
    }
}
