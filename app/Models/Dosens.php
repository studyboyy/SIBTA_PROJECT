<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['photo', 'user_id', 'kuota_bimbingan', 'jabatan', 'nidn', 'phone'])]
class Dosens extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bimbingans()
    {
        return $this->hasMany(Bimbingans::class, 'dosen_id');
    }
}
