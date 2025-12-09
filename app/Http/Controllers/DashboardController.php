<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function indexOwner()
    {
        $totalMember = Member::count();
        $totalPoin = Member::sum('poin');
        $totalTransaksi = Transaksi::whereDate('created_at', today())->count();
        
        // Ambil data untuk chart
        $chartData = $this->getChartData();
        
        return view('Owner.Dashboard', compact(
            'totalMember', 
            'totalPoin',
            'totalTransaksi',
            'chartData'
        ));
    }

    public function indexKasir()
    {
        $totalMember = Member::count();
        $totalPoin = Member::sum('poin');
        $totalTransaksi = Transaksi::whereDate('created_at', today())->count();
        
        // Ambil data untuk chart (bisa dikurangi untuk kasir)
        $chartData = $this->getChartData();
        
        return view('Kasir.Dashboard', compact(
            'totalMember', 
            'totalPoin',
            'totalTransaksi',
            'chartData'
        ));
    }
    
    private function getChartData()
    {
        $dailyData = Transaksi::selectRaw('
                DATE(created_at) as tanggal,
                SUM(jumlah_poin) as total_poin,
                COUNT(*) as jumlah_transaksi
            ')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();
        
        $dailyLabels = [];
        $dailyPoin = [];
        $dailyTransaksi = [];
        
        foreach ($dailyData as $item) {
            $dailyLabels[] = date('d M', strtotime($item->tanggal));
            $dailyPoin[] = $item->total_poin;
            $dailyTransaksi[] = $item->jumlah_transaksi;
        }
        
        // Data bulanan (6 bulan)
        $monthlyData = Transaksi::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as bulan,
                MONTHNAME(created_at) as nama_bulan,
                SUM(jumlah_poin) as total_poin
            ')
            ->where('created_at', '>=', now()->subMonths(5))
            ->groupBy('bulan', 'nama_bulan')
            ->orderBy('bulan')
            ->get();
        
        $monthlyLabels = [];
        $monthlyPoin = [];
        
        foreach ($monthlyData as $item) {
            $monthlyLabels[] = $item->nama_bulan;
            $monthlyPoin[] = $item->total_poin;
        }
        
        // Statistik
        $stats = [
            'avg_poin_per_day' => count($dailyPoin) > 0 ? round(array_sum($dailyPoin) / count($dailyPoin), 1) : 0,
            'max_poin_day' => count($dailyPoin) > 0 ? max($dailyPoin) : 0,
            'total_poin_30_days' => array_sum($dailyPoin),
            'avg_transaksi_per_day' => count($dailyTransaksi) > 0 ? round(array_sum($dailyTransaksi) / count($dailyTransaksi), 1) : 0
        ];
        
        return [
            'daily' => [
                'labels' => $dailyLabels,
                'poin' => $dailyPoin,
                'transaksi' => $dailyTransaksi
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'poin' => $monthlyPoin
            ],
            'stats' => $stats
        ];
    }
    
    public function getChartDataApi(Request $request)
    {
        $days = $request->get('days', 30);
        
        $data = Transaksi::selectRaw('
                DATE(created_at) as tanggal,
                SUM(jumlah_poin) as total_poin
            ')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();
        
        $labels = [];
        $values = [];
        
        foreach ($data as $item) {
            $labels[] = date('d M', strtotime($item->tanggal));
            $values[] = $item->total_poin;
        }
        
        return response()->json([
            'labels' => $labels,
            'data' => $values
        ]);
    }
}