<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poin extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'jumlah_poin',
        'keterangan',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
