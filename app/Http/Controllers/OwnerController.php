<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\Transaksi;
use App\Models\Reward;
use App\Models\Poin;
use App\Models\Laporan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OwnerController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalMember = Member::count();
        $totalPoin = Member::sum('poin');
        $totalTransaksi = Transaksi::whereDate('created_at', today())->count();

        // Ambil filter dari request
        $filter = $request->get('filter', 'current_year');

        // Data grafik transaksi per bulan
        $chartData = $this->getMonthlyTransactionData($filter);

        // Transaksi terbaru untuk dashboard owner juga
        $recentTransactions = Transaksi::with('member')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Label periode untuk tampilan
        $chartPeriod = $this->getChartPeriodLabel($filter);

        return view('owner.dashboard', compact(
            'totalMember',
            'totalPoin',
            'totalTransaksi',
            'chartData',
            'recentTransactions', // TAMBAHKAN INI
            'filter',
            'chartPeriod'
        ));
    }

    /**
     * Ambil data transaksi per bulan berdasarkan filter (SAMA DENGAN KASIR)
     */
    private function getMonthlyTransactionData($filter)
    {
        $now = Carbon::now();

        switch ($filter) {
            case 'current_year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;

            case 'last_year':
                $startDate = $now->copy()->subYear()->startOfYear();
                $endDate = $now->copy()->subYear()->endOfYear();
                break;

            case 'all_time':
            default:
                $firstTransaction = Transaksi::orderBy('created_at')->first();
                $startDate = $firstTransaction ? $firstTransaction->created_at : $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
        }

        // Query transaksi per bulan (JUMLAH TRANSAKSI, bukan poin)
        $transactions = Transaksi::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total_transactions')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Format data untuk chart
        $labels = [];
        $data = [];

        // Generate semua bulan dalam rentang waktu
        $current = $startDate->copy()->startOfMonth();

        while ($current <= $endDate) {
            $monthName = $current->translatedFormat('M Y'); // Format: Jan 2024

            // Cari transaksi untuk bulan ini
            $transaction = $transactions->first(function ($item) use ($current) {
                return $item->year == $current->year && $item->month == $current->month;
            });

            $labels[] = $monthName;
            $data[] = $transaction ? $transaction->total_transactions : 0;

            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'transaksi' => $data,
            'has_data' => array_sum($data) > 0,
            'total_transactions' => array_sum($data)
        ];
    }

    private function getChartPeriodLabel($filter)
    {
        switch ($filter) {
            case 'current_year':
                return 'Tahun ' . date('Y');
            case 'last_year':
                return 'Tahun ' . (date('Y') - 1);
            case 'all_time':
                return 'Semua Waktu';
            default:
                return 'Tahun ' . date('Y');
        }
    }

    public function chartData(Request $request)
    {
        $filter = $request->get('filter', 'current_year');
        $chartData = $this->getMonthlyTransactionData($filter);

        return response()->json([
            'success' => true,
            'labels' => $chartData['labels'],
            'data' => $chartData['transaksi'],
            'filter' => $filter,
            'total' => array_sum($chartData['transaksi'])
        ]);
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
        return [
            'monthly' => [
                'labels' => [],
                'poin' => [],
                'belanja' => [],
                'transaksi' => []
            ],
            'stats' => []
        ];
    }
}