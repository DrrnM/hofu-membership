<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Member;
use App\Models\Poin;
use App\Models\Transaksi;

class KasirController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalMember = Member::count();
        $totalPoin = Member::sum('poin');
        $totalTransaksi = Transaksi::whereDate('created_at', today())->count();
        
        // Ambil filter dari request, default 'current_year'
        $filter = $request->get('filter', 'current_year');
        
        // Data grafik transaksi per bulan (SAMA DENGAN OWNER)
        $chartData = $this->getMonthlyTransactionData($filter);
        
        // Transaksi terbaru untuk tabel
        $recentTransactions = Transaksi::with('member')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Label periode untuk tampilan
        $chartPeriod = $this->getChartPeriodLabel($filter);
        
        \Log::info('Kasir Dashboard accessed', [
            'user' => auth()->user()->username,
            'filter' => $filter,
            'chart_data' => [
                'has_data' => $chartData['has_data'],
                'total_transactions' => $chartData['total_transactions']
            ]
        ]);
        
        return view('kasir.dashboard', compact(
            'totalMember',
            'totalPoin',
            'totalTransaksi',
            'chartData',
            'recentTransactions',
            'filter',
            'chartPeriod'
        ));
    }
    
    /**
     * Ambil data transaksi per bulan berdasarkan filter (SAMA DENGAN OWNER)
     */
    private function getMonthlyTransactionData($filter)
    {
        \Log::info('Kasir getMonthlyTransactionData', ['filter' => $filter]);
        
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
                // Ambil dari transaksi pertama
                $firstTransaction = Transaksi::orderBy('created_at')->first();
                $startDate = $firstTransaction ? $firstTransaction->created_at : $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
        }
        
        // Query transaksi per bulan
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
        
        \Log::info('Kasir transaction query result', [
            'count' => $transactions->count(),
            'period' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')
        ]);
        
        // Format data untuk chart
        $labels = [];
        $data = [];
        
        // Generate semua bulan dalam rentang waktu
        $current = $startDate->copy()->startOfMonth();
        
        while ($current <= $endDate) {
            // GUNAKAN format() bukan translatedFormat() untuk konsistensi
            $monthName = $current->format('M Y'); // Format: Jan 2024
            
            // Cari transaksi untuk bulan ini
            $transaction = $transactions->first(function ($item) use ($current) {
                return $item->year == $current->year && $item->month == $current->month;
            });
            
            $labels[] = $monthName;
            $data[] = $transaction ? $transaction->total_transactions : 0;
            
            $current->addMonth();
        }
        
        \Log::info('Kasir chart data prepared', [
            'labels_count' => count($labels),
            'data_count' => count($data),
            'has_data' => array_sum($data) > 0
        ]);
        
        return [
            'labels' => $labels,
            'transaksi' => $data,
            'has_data' => array_sum($data) > 0,
            'total_transactions' => array_sum($data)
        ];
    }
    
    /**
     * Label periode chart
     */
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
    
    /**
     * API untuk AJAX chart data (SAMA DENGAN OWNER)
     */
    public function chartData(Request $request)
    {
        try {
            \Log::info('Kasir chartData API called', [
                'filter' => $request->get('filter', 'current_year'),
                'user' => auth()->user()->username
            ]);
            
            $filter = $request->get('filter', 'current_year');
            $chartData = $this->getMonthlyTransactionData($filter);
            
            return response()->json([
                'success' => true,
                'labels' => $chartData['labels'],
                'data' => $chartData['transaksi'],
                'filter' => $filter,
                'total' => array_sum($chartData['transaksi'])
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Kasir chartData error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'labels' => [],
                'data' => []
            ], 500);
        }
    }

    public function listMember()
    {
        $members = Member::orderBy('nama')->get();
        return view('kasir.member-list', compact('members'));
    }

    public function createMember()
    {
        return view('kasir.member-create');
    }

    public function storeMember(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15|unique:members,no_hp',
            'poin' => 'nullable|integer|min:0'
        ]);

        // Generate member_id
        $lastMember = Member::orderBy('member_id', 'desc')->first();
        
        if ($lastMember && preg_match('/M(\d+)/', $lastMember->member_id, $matches)) {
            $nextId = intval($matches[1]) + 1;
        } else {
            $nextId = 1;
        }
        
        $idMember = 'M' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        Member::create([
            'member_id' => $idMember,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'poin' => $request->poin ?? 0,
            'tipe_langganan' => Member::getTierByPoin($request->poin ?? 0)
        ]);

        return redirect()->route('kasir.member.list')
            ->with('success', 'Member ' . $request->nama . ' berhasil ditambahkan! (ID: ' . $idMember . ')');
    }

    public function showMember($id)
    {
        $member = Member::where('member_id', $id)->firstOrFail();
        $transactions = Transaksi::where('member_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kasir.member-show', compact('member', 'transactions'));
    }

    public function inputPoin()
    {
        $members = Member::orderBy('nama')->get();
        return view('kasir.poin-input', compact('members'));
    }

    public function processPoin(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'jumlah_poin' => 'required|integer|min:1',
            'type' => 'required|in:earn,redeem',
            'keterangan' => 'required|string|max:255'
        ]);

        \DB::beginTransaction();

        try {
            $member = Member::where('member_id', $request->member_id)->firstOrFail();
            
            // Simpan poin lama untuk log
            $oldPoin = $member->poin;
            $oldTier = $member->tipe_langganan;
            
            if ($request->type === 'earn') {
                $member->poin += $request->jumlah_poin;
                $message = 'Poin berhasil ditambahkan!';
                $poinChange = $request->jumlah_poin;
            } else {
                if ($member->poin < $request->jumlah_poin) {
                    throw new \Exception('Poin member tidak cukup! Poin tersedia: ' . $member->poin);
                }
                $member->poin -= $request->jumlah_poin;
                $message = 'Poin berhasil ditukar!';
                $poinChange = -$request->jumlah_poin;
            }

            // Update tier otomatis
            $newTier = Member::getTierByPoin($member->poin);
            $member->tipe_langganan = $newTier;
            $member->save();

            // Simpan history poin
            Poin::create([
                'member_id' => $request->member_id,
                'jumlah_poin' => $poinChange,
                'keterangan' => $request->keterangan . 
                    ' (Sebelum: ' . $oldPoin . ' poin, Tier: ' . $oldTier . 
                    ' | Sesudah: ' . $member->poin . ' poin, Tier: ' . $newTier . ')'
            ]);

            // Simpan transaksi
            Transaksi::create([
                'member_id' => $request->member_id,
                'jumlah_poin' => $poinChange,
                'total_pembelian' => 0,
                'keterangan' => $request->keterangan
            ]);

            \DB::commit();

            return redirect()->route('kasir.poin.input')
                ->with('success', $message . 
                    '<br>Member: ' . $member->nama . 
                    '<br>Poin sekarang: ' . $member->poin . 
                    ' (' . $member->getLabelLangganan() . ')');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return back()
                ->with('error', 'Gagal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function historyPoin()
    {
        $transactions = Transaksi::with('member')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('kasir.poin-history', compact('transactions'));
    }
    
    /**
     * Method untuk test chart data
     */
    public function testChartData()
    {
        $data = $this->getMonthlyTransactionData('current_year');
        
        return response()->json([
            'debug' => $data,
            'transaksi_count' => Transaksi::count(),
            'first_transaction' => Transaksi::orderBy('created_at')->first()?->created_at,
            'last_transaction' => Transaksi::orderBy('created_at', 'desc')->first()?->created_at
        ]);
    }
}