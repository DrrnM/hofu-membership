<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    // 📋 INDEX + SEARCH BERDASARKAN ID MEMBER (tidak harus spesifik)
    public function index(Request $request)
    {
        $search = $request->input('search');

        $members = Member::when($search, function ($query, $search) {
            return $query->where('id_member', 'like', "%{$search}%");
        })->get();

        return view('Member.index', compact('members'));
    }

    // ➕ CREATE (Owner & Kasir bisa)
    public function create()
    {
        return view('Member.create');
    }

    // 💾 STORE
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20|unique:members,no_hp',
            'poin' => 'nullable|integer|min:0',
        ]);

        // 🔢 Random ID Member 3 digit unik
        do {
            $randomId = rand(100, 999);
        } while (Member::where('id_member', $randomId)->exists());

        Member::create([
            'id_member' => $randomId,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'poin' => $request->poin ?? 0,
        ]);

        return redirect()->route('members.index')->with('success', 'Member berhasil ditambahkan!');
    }

    // 👁️ SHOW (Owner & Kasir bisa)
    public function show($id)
    {
        $member = Member::where('id_member', $id)->firstOrFail();
        return view('Member.show', compact('member'));
    }

    // ✏️ EDIT (Owner & Kasir bisa)
    public function edit($id)
    {
        $member = Member::where('id_member', $id)->firstOrFail();
        return view('Member.edit', compact('member'));
    }

    // 🔄 UPDATE (Owner & Kasir bisa)
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20|unique:members,no_hp,' . $id . ',id_member',
            'poin' => 'nullable|integer|min:0',
        ]);

        $member = Member::where('id_member', $id)->firstOrFail();
        $member->update($request->only(['nama', 'no_hp', 'poin']));

        return redirect()->route('members.index')->with('success', 'Data member berhasil diperbarui!');
    }

    // ❌ DELETE (Owner & Kasir bisa)
    public function destroy($id)
    {
        $member = Member::where('id_member', $id)->firstOrFail();
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member berhasil dihapus!');
    }
}
