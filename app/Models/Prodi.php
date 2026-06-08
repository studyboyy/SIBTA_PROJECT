<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'code', 'kaprodi_user_id'])]
class Prodi extends Model
{
    use HasFactory;

    public function kaprodiUser()
    {
        return $this->belongsTo(User::class, 'kaprodi_user_id');
    }

    public function mahasiswas()
    {
        return $this->hasMany(Mahasiswas::class, 'prodi_id');
    }

    public function sidangBatches()
    {
        return $this->hasMany(SidangBatch::class, 'prodi_id');
    }
}
