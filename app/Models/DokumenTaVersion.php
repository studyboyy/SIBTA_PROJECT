<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['dokumen_ta_id', 'uploaded_by_user_id', 'uploader_role', 'action', 'file', 'note', 'status_snapshot'])]
class DokumenTaVersion extends Model
{
    protected $table = 'dokumen_ta_versions';

    public function dokumen()
    {
        return $this->belongsTo(DokumenTa::class, 'dokumen_ta_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
