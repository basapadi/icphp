<?php

namespace App\Models;
class RoleMenu extends BaseModel
{
    public $timestamps = false;
    protected $fillable = [
        'role',
        'menu_id',
        'view',
        'create',
        'edit',
        'update',
        'delete',
        'download'
    ];

    public function menu(){
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
