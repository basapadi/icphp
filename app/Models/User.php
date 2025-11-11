<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Exception;
class User extends BaseUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role',
        'active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['activeLabel','status'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getActiveLabelAttribute()
    {
        return $this->active ? 'Aktif' : 'Tidak Aktif';
    }

    public function getStatusAttribute()
    {
        return $this->active;
    }

    public function itemReceiveds()
    {
        return $this->hasMany(ItemReceived::class,'created_by');
    }

    public function itemSales()
    {
        return $this->hasMany(ItemDelivery::class,'created_by');
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            if ($data->itemReceiveds()->exists()) {
                throw new Exception('Tidak dapat menghapus pengguna ini karena sudah pernah melakukan transaksi');
            }

            if ($data->itemReceivedPayments()->exists()) {
                throw new Exception('Tidak dapat menghapus pengguna ini karena sudah pernah melakukan pembelian');
            }

            if ($data->itemSales()->exists()) {
                throw new Exception('Tidak dapat menghapus pengguna ini karena sudah pernah melakukan transaksi');
            }

            if ($data->itemSalePayments()->exists()) {
                throw new Exception('Tidak dapat menghapus pengguna ini karena sudah pernah melakukan penjualan');
            }
        });
    }
}
