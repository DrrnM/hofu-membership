<?php

namespace App\Http\Controllers;

use App\Models\Poin;
use App\Models\Member;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class PoinController extends Controller
{
    public function index(Request $request) // ✅ Tambah Request $request
    {
        $search = $request->input('search');
        
        $members = Member::select('id_member', 'nama', 'poin', 'tipe_langganan', 'no_hp')
            ->when($search, function ($query, $search) {
                return $query->where('id_member', 'like', "%{$search}%")
                             ->orWhere('nama', 'like', "%{$search}%");
            })
            ->orderBy('poin', 'desc')
            ->paginate(10);

        return view('owner.poin.index', compact('members'));
    }

    public function updatePoinForm($id_member)
    {
        $member = Member::where('id_member', $id_member)->firstOrFail();
        return view('owner.poin.edit', compact('member'));
    }

    public function updatePoin(Request $request, $id_member)
    {
        $request->validate([
            'jumlah_poin' => 'required|numeric|min:1',
            'type' => 'required|in:tambah,kurang'
        ]);

        $member = Member::where('id_member', $id_member)->firstOrFail();

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

    public function edit($id_member)
    {
        $member = Member::where('id_member', $id_member)->firstOrFail();
        return view('owner.poin.edit', compact('member'));
    }

    public function update(Request $request, $id_member)
    {
        $member = Member::where('id_member', $id_member)->firstOrFail();

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

    public function showMemberHistory(Request $request, $id_member) // ✅ Optional: tambah search di history juga
    {
        $search = $request->input('search');
        
        $member = Member::where('id_member', $id_member)->firstOrFail();
        
        $poins = Poin::where('member_id', $id_member)
            ->when($search, function ($query, $search) {
                return $query->where('keterangan', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('poins.member-history', compact('member', 'poins'));
    }
}