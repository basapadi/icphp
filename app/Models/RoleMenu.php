<?php

namespace App\Models;

use Btx\Query\Model;

class RoleMenu extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'role',
        'menu_id',
        'view',
        'create',
        'delete',
        'download'
    ];

    public function menu(){
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
