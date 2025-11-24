<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Member;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Display listing for reports (OWNER & KASIR)
     */
    public function index()
    {
        $transactions = Transaksi::with('member')
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate(20);

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Today's transactions report (KASIR)
     */
    public function todayTransactions()
    {
        $today = now()->format('Y-m-d');
        $transactions = Transaksi::with('member')
            ->whereDate('tanggal_transaksi', $today)
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $totalPoin = $transactions->sum('jumlah_poin');
        $totalTransactions = $transactions->count();

        return view('transactions.today', compact('transactions', 'totalPoin', 'totalTransactions'));
    }

    /**
     * Transaction history for specific member
     */
    public function memberHistory($id_member)
    {
        $member = Member::where('id_member', $id_member)->firstOrFail();
        $transactions = Transaksi::where('id_member', $id_member)
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        return view('transactions.member-history', compact('member', 'transactions'));
    }
}