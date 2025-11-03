<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::all();
        $username = Auth::user()->username ?? 'kasir';

        // Tentukan tampilan berdasarkan username
        if ($username === 'owner') {
            return view('Owner.Member.index', compact('members'));
        } else {
            return view('Kasir.Member.index', compact('members'));
        }
    }

    public function create()
    {
        // hanya owner yang bisa tambah data
        if (Auth::user()->username !== 'owner') {
            abort(403, 'Akses ditolak: hanya owner yang dapat menambah member.');
        }

        return view('Owner.Member.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->username !== 'owner') {
            abort(403, 'Akses ditolak: hanya owner yang dapat menambah member.');
        }

        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20|unique:members,no_hp',
            'poin' => 'nullable|integer|min:0',
        ]);

        Member::create($request->all());
        return redirect()->route('members.index')->with('success', 'Member berhasil ditambahkan!');
    }

    public function edit($id)
    {
        if (Auth::user()->username !== 'owner') {
            abort(403, 'Akses ditolak: hanya owner yang dapat mengedit member.');
        }

        $member = Member::findOrFail($id);
        return view('Owner.Member.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->username !== 'owner') {
            abort(403, 'Akses ditolak: hanya owner yang dapat mengupdate member.');
        }

        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20|unique:members,no_hp,' . $id,
            'poin' => 'nullable|integer|min:0',
        ]);

        $member = Member::findOrFail($id);
        $member->update($request->all());

        return redirect()->route('members.index')->with('success', 'Member berhasil diperbarui!');
    }

    public function destroy($id)
    {
        if (Auth::user()->username !== 'owner') {
            abort(403, 'Akses ditolak: hanya owner yang dapat menghapus member.');
        }

        $member = Member::findOrFail($id);
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member berhasil dihapus!');
    }
}
