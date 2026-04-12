<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $table = 'tb_member';

    protected $primaryKey = 'id_member';

    protected $guarded = [];

    protected $hidden = ['password_member'];

    protected $casts = [
        'saldo' => 'integer',
        'status_aktif' => 'integer',
    ];

    public function kendaraan(): HasMany
    {
        return $this->hasMany(Kendaraan::class, 'id_member', 'id_member');
    }
}
