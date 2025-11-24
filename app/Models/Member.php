<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_member';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($member) {
            if ($member->poin > 500) {
                $member->poin = 500;
            }
        });
    }

    protected $fillable = [
        'id_member',
        'nama',
        'no_hp',
        'poin',
        'tipe_langganan'
    ];

    public $timestamps = true;

    const BRONZE = 'bronze';
    const SILVER = 'silver';
    const GOLD = 'gold';
    const PLATINUM = 'platinum';
    const DIAMOND = 'diamond';

    const BATAS_POIN = [
        'bronze' => 0,
        'silver' => 150,
        'gold' => 300,
        'platinum' => 450,
        'diamond' => 600
    ];

    /**
     * ✅ METHOD TUNGGAL - tanpa duplikasi
     */
    public static function getTierByPoin($poin)
    {
        if ($poin >= self::BATAS_POIN['diamond']) {
            return self::DIAMOND;
        } elseif ($poin >= self::BATAS_POIN['platinum']) {
            return self::PLATINUM;
        } elseif ($poin >= self::BATAS_POIN['gold']) {
            return self::GOLD;
        } elseif ($poin >= self::BATAS_POIN['silver']) {
            return self::SILVER;
        } else {
            return self::BRONZE;
        }
    }

    /**
     * ✅ METHOD TUNGGAL - update tier dan save ke database
     */
    public function updateTierOtomatis()
    {
        $tierBaru = self::getTierByPoin($this->poin);

        // Update field dan save
        $this->tipe_langganan = $tierBaru;
        return $this->save();
    }

    public function getColorBadge()
    {
        return match ($this->tipe_langganan) {
            'bronze' => 'dark',
            'silver' => 'secondary',
            'gold' => 'warning',
            'platinum' => 'info',
            'diamond' => 'primary',
            default => 'secondary'
        };
    }

    public function getLabelLangganan()
    {
        return match ($this->tipe_langganan) {
            'bronze' => 'Bronze',
            'silver' => 'Silver',
            'gold' => 'Gold',
            'platinum' => 'Platinum',
            'diamond' => 'Diamond',
            default => 'Bronze'
        };
    }

    public function poinHistory()
    {
        return $this->hasMany(Poin::class, 'member_id', 'id_member');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_member', 'id_member');
    }
}