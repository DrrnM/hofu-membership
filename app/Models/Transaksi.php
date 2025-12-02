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
    public $timestamps = true;

    protected $fillable = [
        'member_id',
        'total_pembelian',
        'jumlah_poin',
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function poins()
    {
        return $this->hasMany(Poin::class, 'transaksi_id', 'id');
    }

    protected static function booted()
    {
        static::created(function ($transaksi) {
            $transaksi->updateMemberPoints();
        });

        static::updated(function ($transaksi) {
            if ($transaksi->isDirty('total_pembelian')) {
                $transaksi->updateMemberPoints();
            }
        });
    }

    /**
     * Hitung dan update poin
     */
    public function updateMemberPoints()
    {
        try {
            // 1. Hitung poin
            $poinTransaksi = floor($this->total_pembelian / 10000);
            
            // 2. Simpan poin di transaksi
            $this->jumlah_poin = $poinTransaksi;
            $this->saveQuietly();

            // 3. Update poin di member
            $member = $this->member;
            if ($member) {
                $member->increment('poin', $poinTransaksi);
                
                // 4. ✅ PERBAIKI: Gunakan $this->id (bukan $this->id_transaksi)
                \App\Models\Poin::create([
                    'member_id' => $this->member_id,
                    'transaksi_id' => $this->id, // ✅ $this->id BUKAN $this->id_transaksi
                    'jumlah_poin' => $poinTransaksi
                ]);
                
                // 5. Update tier member
                $member->updateTierOtomatis();
                
                \Log::info("✅ Poin diupdate - Transaksi: {$this->id}, Member: {$this->member_id}, Poin: +{$poinTransaksi}");
                
                return $poinTransaksi;
            }
            
            return 0;
            
        } catch (\Exception $e) {
            // ✅ PERBAIKI: Gunakan $this->id
            \Log::error("❌ Gagal update poin untuk transaksi {$this->id}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Accessor: Hitung poin jika null
     */
    public function getJumlahPoinAttribute($value)
    {
        if ($value === null && $this->total_pembelian) {
            $calculated = floor($this->total_pembelian / 10000);
            
            if ($this->exists) {
                $this->jumlah_poin = $calculated;
                $this->saveQuietly();
            }
            
            return $calculated;
        }
        
        return $value;
    }
}