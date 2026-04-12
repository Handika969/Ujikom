<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifikasiMember extends Model
{
    protected $table = 'tb_notifikasi_member';

    protected $primaryKey = 'id_notifikasi';

    protected $guarded = [];
}
