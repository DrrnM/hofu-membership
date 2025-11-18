<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $members = Member::when($search, function ($query, $search) {
            return $query->where('id_member', 'like', "%{$search}%")
                         ->orWhere('nama', 'like', "%{$search}%")
                         ->orWhere('no_hp', 'like', "%{$search}%");
        })->get();

        return view('Member.index', compact('members'));
    }

    public function create()
    {
        return view('Member.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20|unique:members,no_hp',
            'poin' => 'nullable|integer|min:0',
        ]);

        // Generate unique ID
        do {
            $randomId = rand(100, 999);
        } while (Member::where('id_member', $randomId)->exists());

        $poin = $request->poin ?? 0;
        $tipeLangganan = Member::getTierByPoin($poin);

        Member::create([
            'id_member' => $randomId,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'poin' => $poin,
            'tipe_langganan' => $tipeLangganan, 
        ]);

        return redirect()->route('members.index')->with('success', 'Member berhasil ditambahkan! Tier: ' . ucfirst($tipeLangganan));
    }

    public function show($id)
    {
        $member = Member::where('id_member', $id)->firstOrFail();
        return view('Member.show', compact('member'));
    }

    public function edit($id)
    {
        $member = Member::where('id_member', $id)->firstOrFail();
        return view('Member.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20|unique:members,no_hp,' . $id . ',id_member',
            'poin' => 'nullable|integer|min:0',
        ]);

        $member = Member::where('id_member', $id)->firstOrFail();
        
        // Simpan tier lama sebelum update
        $oldTier = $member->tipe_langganan;
        
        // Update data member
        $member->update($request->only(['nama', 'no_hp', 'poin']));
        
        // Update tier otomatis (tanpa menyimpan return value)
        $member->updateTierOtomatis();
        
        // Refresh data untuk mendapatkan tier terbaru
        $member->refresh();
        $newTier = $member->tipe_langganan;
        
        // Buat pesan sukses
        $message = 'Data member berhasil diperbarui!';
        if ($oldTier !== $newTier) {
            $message .= ' Tier berubah menjadi: ' . $member->getLabelLangganan();
        }

        return redirect()->route('members.index')->with('success', $message);
    }

    public function destroy($id)
    {
        $member = Member::where('id_member', $id)->firstOrFail();
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member berhasil dihapus!');
    }
}