<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\Poin;
use App\Models\Transaksi;

class KasirController extends Controller
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
            'no_hp' => 'required|string|max:15',
            'poin' => 'nullable|integer|min:0'
        ]);

        $lastMember = Member::orderBy('id_member', 'desc')->first();
        $nextId = $lastMember ? intval(substr($lastMember->id_member, 1)) + 1 : 1;
        $idMember = 'M' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        Member::create([
            'id_member' => $idMember,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'poin' => $request->poin ?? 0,
            'tipe_langganan' => Member::getTierByPoin($request->poin ?? 0)
        ]);

        return redirect()->route('kasir.member.list')
                        ->with('success', 'Member berhasil ditambahkan!');
    }

    public function showMember($id)
    {
        $member = Member::where('id_member', $id)->firstOrFail();
        $transactions = Transaksi::where('id_member', $id)
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
            'member_id' => 'required|exists:member,id_member',
            'jumlah_poin' => 'required|integer|min:1',
            'type' => 'required|in:earn,redeem',
            'keterangan' => 'required|string|max:255'
        ]);

        $member = Member::where('id_member', $request->member_id)->firstOrFail();

        if ($request->type === 'earn') {
            $member->poin += $request->jumlah_poin;
            $message = 'Poin berhasil ditambahkan!';
        } else {
            if ($member->poin < $request->jumlah_poin) {
                return back()->with('error', 'Poin member tidak cukup!');
            }
            $member->poin -= $request->jumlah_poin;
            $message = 'Poin berhasil ditukar!';
        }

        $member->tipe_langganan = Member::getTierByPoin($member->poin);
        $member->save();

        Poin::create([
            'member_id' => $request->member_id,
            'jumlah_poin' => $request->type === 'earn' ? $request->jumlah_poin : -$request->jumlah_poin,
            'keterangan' => $request->keterangan
        ]);

        Transaksi::create([
            'id_member' => $request->member_id,
            'created_at' => now(),
            'jumlah_poin' => $request->type === 'earn' ? $request->jumlah_poin : -$request->jumlah_poin,
            'total_harga' => 0
        ]);

        return redirect()->route('kasir.poin.input')
                        ->with('success', $message);
    }

    public function historyPoin()
    {
        $transactions = Transaksi::with('member')
                               ->orderBy('created_at', 'desc')
                               ->get();

        return view('kasir.poin-history', compact('transactions'));
    }
}