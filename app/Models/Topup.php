<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topup extends Model
{
    protected $table = 'tb_topup';

    protected $primaryKey = 'id_topup';

    protected $guarded = [];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by', 'id_user');
    }
}
