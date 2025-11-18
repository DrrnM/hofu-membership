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
        'tanggal_laporan', 
        'keterangan',
        'total_transaksi',
        'file_path',
        'file_name',
        'created_at',
        'updated_at'
    ];
}