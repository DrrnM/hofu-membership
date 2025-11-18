<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_member',
        'nama', 
        'no_hp',
        'poin',
        'tipe_langganan'
    ];

    const BRONZE = 'bronze';
    const SILVER = 'silver'; 
    const GOLD = 'gold';
    const PLATINUM = 'platinum';
    const DIAMOND = 'diamond';

    const BATAS_POIN = [
        'bronze' => 0,
        'silver' => 100,
        'gold' => 500,
        'platinum' => 1000,
        'diamond' => 2000
    ];

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

    public function updateTierOtomatis()
    {
        $tierBaru = self::getTierByPoin($this->poin);
        
        if ($this->tipe_langganan !== $tierBaru) {
            $this->tipe_langganan = $tierBaru;
            $this->save();
            return true; 
        }
        
        return false; 
    }

    public function getColorBadge()
    {
        return match($this->tipe_langganan) {
            'bronze' => 'secondary',
            'silver' => 'light',
            'gold' => 'warning',
            'platinum' => 'info',
            'diamond' => 'primary',
            default => 'secondary'
        };
    }

    public function getLabelLangganan()
    {
        return match($this->tipe_langganan) {
            'bronze' => 'Bronze',
            'silver' => 'Silver',
            'gold' => 'Gold', 
            'platinum' => 'Platinum',
            'diamond' => 'Diamond',
            default => 'Bronze'
        };
    }

    public function getInfoTierBerikutnya()
    {
        $currentTier = $this->tipe_langganan;
        $batas = self::BATAS_POIN;
        
        switch ($currentTier) {
            case 'bronze':
                return ['tier' => 'Silver', 'poin_dibutuhkan' => $batas['silver'] - $this->poin];
            case 'silver':
                return ['tier' => 'Gold', 'poin_dibutuhkan' => $batas['gold'] - $this->poin];
            case 'gold':
                return ['tier' => 'Platinum', 'poin_dibutuhkan' => $batas['platinum'] - $this->poin];
            case 'platinum':
                return ['tier' => 'Diamond', 'poin_dibutuhkan' => $batas['diamond'] - $this->poin];
            case 'diamond':
                return ['tier' => 'Max', 'poin_dibutuhkan' => 0];
            default:
                return ['tier' => 'Silver', 'poin_dibutuhkan' => $batas['silver'] - $this->poin];
        }
    }
}