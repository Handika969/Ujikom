<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoMutasi extends Model
{
    protected $table = 'tb_saldo_mutasi';

    protected $primaryKey = 'id_mutasi';

    protected $guarded = [];
}
