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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {

            $last = Member::orderBy('id_member', 'desc')->first();

            $next = $last ? ((int) $last->id_member) + 1 : 1;

 
            $member->id_member = str_pad($next, 3, '0', STR_PAD_LEFT);
        });
    }
}
