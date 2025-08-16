<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $this->hasOne(Menu::class,'id');
    }
}
