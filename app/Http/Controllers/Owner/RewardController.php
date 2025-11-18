<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reward;

class RewardController extends Controller
{
    public function index()
    {
        $rewards = Reward::all();
        return view('Owner.Reward.index', compact('rewards'));
    }

    public function create()
    {
        return view('Owner.Reward.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_reward' => 'required|string|max:100',
            'poin_diperlukan' => 'required|numeric|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        Reward::create([
            'nama_reward' => $request->nama_reward,
            'poin_diperlukan' => $request->poin_diperlukan,
            'deskripsi' => $request->deskripsi,
            'tanggal_dibuat' => now(),
        ]);

        return redirect()->route('owner.reward.index')->with('success', 'Reward berhasil ditambahkan!');
    }

    // ✅ TAMBAHKAN METHOD SHOW INI
    public function show($id)
    {
        $reward = Reward::findOrFail($id);
        return view('Owner.Reward.show', compact('reward'));
    }

    public function edit($id)
    {
        $reward = Reward::findOrFail($id);
        return view('Owner.Reward.edit', compact('reward'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_reward' => 'required|string|max:100',
            'poin_diperlukan' => 'required|numeric|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        $reward = Reward::findOrFail($id);
        $reward->update($request->only(['nama_reward', 'poin_diperlukan', 'deskripsi']));

        return redirect()->route('owner.reward.index')->with('success', 'Reward berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $reward = Reward::findOrFail($id);
        $reward->delete();
        return redirect()->route('owner.reward.index')->with('success', 'Reward berhasil dihapus!');
    }
}