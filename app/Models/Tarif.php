<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarif extends Model
{
    protected $table = 'tb_tarif';

    protected $primaryKey = 'id_tarif';

    protected $guarded = [];

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_tarif', 'id_tarif');
    }
}
