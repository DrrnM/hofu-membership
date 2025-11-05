<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_member',
        'tanggal_transaksi',
        'jumlah_poin',
        'total_harga',
    ];

    // 🔗 Relasi balik ke Member
    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }
}
