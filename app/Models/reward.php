<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $table = 'reward';
    protected $primaryKey = 'id_reward';
    public $timestamps = false;

    protected $fillable = [
        'nama_reward',
        'poin_diperlukan',
        'deskripsi',
        'tanggal_dibuat',
    ];
}
