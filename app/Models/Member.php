<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members';
    protected $primaryKey = 'id_member';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_member', 'nama', 'no_hp', 'poin'];
}
