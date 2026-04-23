<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'bimbingan_log_id',
    'changed_by_user_id',
    'from_status_sesi',
    'to_status_sesi',
    'source',
    'note',
    'changed_at',
])]
class BimbinganSessionAudit extends Model
{
    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function bimbinganLog()
    {
        return $this->belongsTo(BimbinganLog::class, 'bimbingan_log_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
