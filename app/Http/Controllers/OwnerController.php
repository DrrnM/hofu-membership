<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\Transaksi;
use App\Models\Reward;
use App\Models\Poin;
use App\Models\Laporan;

class OwnerController extends Controller
{
    // 📊 DASHBOARD OWNER
    public function dashboard()
    {
        $totalMember = Member::count();
        $totalPoin = Member::sum('poin');
        $totalTransaksi = Transaksi::whereDate('created_at', today())->count();

        return view('owner.dashboard', compact(
            'totalMember', 
            'totalPoin', 
            'totalTransaksi',
        ));
    }

    // 👑 LAPORAN SEMUA MEMBER
    public function laporanMember()
    {
        $members = Member::withCount(['transaksi as total_transaksi'])
                      ->withSum('transaksi as total_poin_transaksi', 'jumlah_poin')
                      ->orderBy('poin', 'desc')
                      ->get();

        return view('owner.laporan-member', compact('members'));
    }

    // 👑 LAPORAN SEMUA TRANSAKSI
    public function laporanTransaksi()
    {
        $transactions = Transaksi::with('member')
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('owner.laporan-transaksi', compact('transactions'));
    }

    // 👑 LAPORAN POIN
    public function laporanPoin()
    {
        $poinHistory = Poin::with('member')
                          ->orderBy('created_at', 'desc')
                          ->get();

        return view('owner.laporan-poin', compact('poinHistory'));
    }

    // 👑 MANAGE REWARD
    public function manageReward()
    {
        $rewards = Reward::all();
        return view('owner.manage-reward', compact('rewards'));
    }

    // 👑 TAMBAH REWARD
    public function tambahReward(Request $request)
    {
        $request->validate([
            'nama_reward' => 'required|string|max:255',
            'poin_diperlukan' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string'
        ]);

        Reward::create([
            'nama_reward' => $request->nama_reward,
            'poin_diperlukan' => $request->poin_diperlukan,
            'deskripsi' => $request->deskripsi,
            'tanggal_dibuat' => now()
        ]);

        return redirect()->route('owner.manage-reward')
                        ->with('success', 'Reward berhasil ditambahkan!');
    }
}