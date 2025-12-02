<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'judul_laporan', 
        'file_name',
        'file_path',
        'total_transaksi',
        'total_data', 
        'tanggal_laporan',
        'periode_laporan', 
        'keterangan'
    ];
    protected $casts = [
        'tanggal_laporan' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
