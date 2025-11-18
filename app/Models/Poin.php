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

    // PERBAIKAN: Specify foreign key dan owner key
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id_member');
    }
}