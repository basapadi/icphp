<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Trash extends BaseModel
{
    protected $fillable = [
        'module',
        'data',
        'schema',
        'created_by',
        'can_rollback'
    ];

    protected $appends = ['module_label','created_at_formatted','short_data'];

    public function getModuleLabelAttribute()
    {
        $config = config('ihandcashier.menus');
        $menu = collect($config)->where('module', $this->module)->first();
        return $menu['label'];
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? Carbon::parse($this->created_at)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    public function getShortDataAttribute()
    {
        return substr($this->data, 0, 150) .' ...';
    }

    public function user(){
       return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
