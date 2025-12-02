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
    public function laporanMember()
    {
        $members = Member::withCount(['transaksi as total_transaksi'])
            ->withSum('transaksi as total_poin_transaksi', 'jumlah_poin')
            ->orderBy('poin', 'desc')
            ->get();

        return view('owner.laporan-member', compact('members'));
    }

    public function laporanTransaksi()
    {
        $transactions = Transaksi::with('member')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('owner.laporan-transaksi', compact('transactions'));
    }

    public function laporanPoin()
    {
        $poinHistory = Poin::with('member')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('owner.laporan-poin', compact('poinHistory'));
    }

    public function manageReward()
    {
        $rewards = Reward::all();
        return view('owner.manage-reward', compact('rewards'));
    }

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
    private function getChartData()
    {
        // DATA BULANAN (12 bulan terakhir)
        $monthlyData = Transaksi::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as bulan,
            MONTHNAME(created_at) as nama_bulan,
            MONTH(created_at) as bulan_angka,
            SUM(jumlah_poin) as total_poin,
            SUM(total_pembelian) as total_belanja,
            COUNT(*) as jumlah_transaksi
        ')
            ->where('created_at', '>=', now()->subMonths(11))
            ->groupBy('bulan', 'nama_bulan', 'bulan_angka')
            ->orderBy('bulan')
            ->get();

        // Format label bulan
        $monthlyLabels = [];
        $monthlyPoin = [];
        $monthlyBelanja = [];
        $monthlyTransaksi = [];

        // Buat array untuk semua bulan
        $allMonths = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $allMonths[date('Y-m', strtotime($date))] = [
                'label' => date('M Y', strtotime($date)),
                'poin' => 0,
                'belanja' => 0,
                'transaksi' => 0
            ];
        }

        // Isi data dari database
        foreach ($monthlyData as $item) {
            $allMonths[$item->bulan] = [
                'label' => date('M Y', strtotime($item->bulan . '-01')),
                'poin' => $item->total_poin,
                'belanja' => $item->total_belanja,
                'transaksi' => $item->jumlah_transaksi
            ];
        }

        // Urutkan dari bulan tertua ke terbaru
        ksort($allMonths);

        foreach ($allMonths as $data) {
            $monthlyLabels[] = $data['label'];
            $monthlyPoin[] = $data['poin'];
            $monthlyBelanja[] = $data['belanja'];
            $monthlyTransaksi[] = $data['transaksi'];
        }

        // STATISTIK
        $activeMonths = array_filter($monthlyPoin, function ($poin) {
            return $poin > 0;
        });

        $highestMonthIndex = array_search(max($monthlyPoin), $monthlyPoin);
        $highestMonth = $highestMonthIndex !== false ? $monthlyLabels[$highestMonthIndex] : '-';

        $stats = [
            'highest_month' => $highestMonth,
            'highest_month_poin' => max($monthlyPoin),
            'avg_poin_per_month' => count($activeMonths) > 0 ? round(array_sum($monthlyPoin) / count($activeMonths)) : 0,
            'total_poin_this_year' => array_sum($monthlyPoin),
            'active_months' => count($activeMonths),
            'total_belanja_this_year' => array_sum($monthlyBelanja)
        ];

        return [
            'monthly' => [
                'labels' => $monthlyLabels,
                'poin' => $monthlyPoin,
                'belanja' => $monthlyBelanja,
                'transaksi' => $monthlyTransaksi
            ],
            'stats' => $stats
        ];
    }

    public function chartData(Request $request)
    {
        $filter = $request->get('filter', 'current_year');

        if ($filter === 'last_year') {
            // Data tahun lalu
            $data = Transaksi::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as bulan,
                MONTHNAME(created_at) as nama_bulan,
                SUM(jumlah_poin) as total_poin
            ')
                ->whereYear('created_at', date('Y') - 1)
                ->groupBy('bulan', 'nama_bulan')
                ->orderBy('bulan')
                ->get();
        } elseif ($filter === 'all_time') {
            // Semua data per bulan
            $data = Transaksi::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as bulan,
                MONTHNAME(created_at) as nama_bulan,
                SUM(jumlah_poin) as total_poin
            ')
                ->groupBy('bulan', 'nama_bulan')
                ->orderBy('bulan')
                ->get();
        } else {
            // Tahun ini (default)
            $data = Transaksi::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as bulan,
                MONTHNAME(created_at) as nama_bulan,
                SUM(jumlah_poin) as total_poin
            ')
                ->whereYear('created_at', date('Y'))
                ->groupBy('bulan', 'nama_bulan')
                ->orderBy('bulan')
                ->get();
        }

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $labels[] = $item->nama_bulan . ' ' . date('Y', strtotime($item->bulan . '-01'));
            $values[] = $item->total_poin;
        }

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'data' => $values
        ]);
    }
}