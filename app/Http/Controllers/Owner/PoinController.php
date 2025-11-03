<?php

namespace App\Http\Controllers;

use App\Models\Poin;
use App\Models\Member;
use Illuminate\Http\Request;

class PoinController extends Controller
{
    public function index()
    {
        $poins = Poin::with('member')->paginate(10);
        return view('owner.poin.index', compact('poins'));
    }

    public function edit(Poin $poin)
    {
        return view('owner.poin.edit', compact('poin'));
    }

    public function update(Request $request, Poin $poin)
    {
        $request->validate([
            'jumlah_poin' => 'required|integer',
        ]);

        $poin->update($request->all());
        return redirect()->route('poins.index')->with('success', 'Poin berhasil diperbarui');
    }
}
