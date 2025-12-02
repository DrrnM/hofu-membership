<?php

namespace App\Http\Controllers;

use App\Models\Poin;
use App\Models\Member;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class PoinController extends Controller
{
    public function index(Request $request) 
    {
        $search = $request->input('search');

        $members = Member::select('member_id', 'nama', 'poin', 'tipe_langganan', 'no_hp')
            ->when($search, function ($query, $search) {
                return $query->where('member_id', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('owner.poin.index', compact('members'));
    }

    public function updatePoinForm($member_id)
    {
        $member = Member::where('member_id', $member_id)->firstOrFail();
        return view('owner.poin.edit', compact('member'));
    }

    public function updatePoin(Request $request, $member_id)
    {
        $request->validate([
            'jumlah_poin' => 'required|numeric|min:1',
            'type' => 'required|in:tambah,kurang'
        ]);

        $member = Member::where('member_id', $member_id)->firstOrFail();

        if ($request->type === 'tambah') {
            $newPoin = $member->poin + $request->jumlah_poin;
            // ✅ Cek maksimal 500 poin
            if ($newPoin > 500) {
                return back()->with('error', 'Poin tidak boleh melebihi 500!');
            }
            $member->poin = $newPoin;
        } else {
            if ($member->poin < $request->jumlah_poin) {
                return back()->with('error', 'Poin tidak cukup!');
            }
            $member->poin -= $request->jumlah_poin;
        }

        $member->updateTierOtomatis();
        $member->save();

        return redirect()->route('poins.index')
            ->with('success', 'Poin berhasil diperbarui!');
    }

    public function edit($member_id)
    {
        $member = Member::where('member_id', $member_id)->firstOrFail();
        return view('owner.poin.edit', compact('member'));
    }

    public function update(Request $request, $member_id)
    {
        $member = Member::where('member_id', $member_id)->firstOrFail();

        $request->validate([
            'poin' => 'required|numeric|min:0|max:500'  // ✅ Tambah max:500
        ]);

        $member->update([
            'poin' => $request->poin
        ]);

        // Update tier otomatis
        $member->updateTierOtomatis();

        return redirect()->route('poins.index')
            ->with('success', 'Poin berhasil diperbarui.');
    }

    public function showMemberHistory(Request $request, $member_id) // ✅ Optional: tambah search di history juga
    {
        $search = $request->input('search');

        $member = Member::where('member_id', $member_id)->firstOrFail();

        $poins = Poin::where('member_id', $member_id)
            ->when($search, function ($query, $search) {
                return $query->where('keterangan', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('poins.member-history', compact('member', 'poins'));
    }
}