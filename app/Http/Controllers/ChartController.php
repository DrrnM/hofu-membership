<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index()
    {
        // Data untuk chart
        $poinData = $this->getPoinData();

        return view('charts.index', compact('poinData'));
    }

    private function getPoinData()
    {
        return Transaksi::selectRaw('
                DATE(created_at) as tanggal,
                SUM(jumlah_poin) as total_poin,
                COUNT(*) as jumlah_transaksi,
                SUM(total_pembelian) as total_belanja
            ')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();
    }

    private function getChartData()
    {
        // DATA DEFAULT: SEMUA WAKTU
        $monthlyData = Transaksi::selectRaw('
        DATE_FORMAT(created_at, "%Y-%m") as bulan,
        MONTHNAME(created_at) as nama_bulan,
        MONTH(created_at) as bulan_angka,
        YEAR(created_at) as tahun,
        SUM(jumlah_poin) as total_poin,
        SUM(total_pembelian) as total_belanja,
        COUNT(*) as jumlah_transaksi
    ')
            ->groupBy('bulan', 'nama_bulan', 'bulan_angka', 'tahun')
            ->orderBy('tahun')
            ->orderBy('bulan_angka')
            ->get();

        // Format label bulan
        $monthlyLabels = [];
        $monthlyPoin = [];
        $monthlyBelanja = [];
        $monthlyTransaksi = [];

        foreach ($monthlyData as $item) {
            $monthlyLabels[] = $item->nama_bulan . ' ' . $item->tahun;
            $monthlyPoin[] = $item->total_poin;
            $monthlyBelanja[] = $item->total_belanja;
            $monthlyTransaksi[] = $item->jumlah_transaksi;
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
            'total_poin_all_time' => array_sum($monthlyPoin),
            'active_months' => count($activeMonths),
            'total_belanja_all_time' => array_sum($monthlyBelanja)
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
}