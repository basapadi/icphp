<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Role extends BaseModel
{
    public $timestamps = false;
    protected $appends = ['is_admin_label'];
    public $table = 'roles';
    protected $fillable = [
        'key',
        'name',
        'is_admin',
    ];

    public function getIsAdminLabelAttribute()
    {
        return $this->is_admin ? 'Ya' : 'Tidak';
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            if (in_array($data->key, ['administrator'])) {
                throw new Exception('Grup ini tidak dapat dihapus karena top level management.');
            }

            // Tidak boleh menghapus grup user sendiri
            $user = Auth::user();

            if ($user && $user->hasRole($data->key)) {
                throw new Exception('Anda tidak dapat menghapus role anda sendiri.');
            }

            // Harus ada grup lain dengan is_admin minimal 1
            if ($data->is_admin) {
                $countOtherAdmin = Role::where('is_admin', true)
                    ->where('id', '!=', $data->id)
                    ->count();
                if ($countOtherAdmin === 0) {
                    throw new Exception('Minimal harus ada 1 grup admin lain. Penghapusan dibatalkan.');
                }
            }

            DB::table('role_menus')->where('role', $data->key)->delete();
        });
    }
}
