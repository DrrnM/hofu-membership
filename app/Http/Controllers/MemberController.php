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
            return $query->where('member_id', 'like', "%{$search}%")
                ->orWhere('nama', 'like', "%{$search}%");
        })->orderBy('created_at', 'desc')->paginate(10);

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

        do {
            $randomId = rand(100, 999);
        } while (Member::where('member_id', $randomId)->exists());

        $poin = $request->poin ?? 0;
        $tipeLangganan = Member::getTierByPoin($poin);

        Member::create([
            'member_id' => $randomId,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'poin' => $poin,
            'tipe_langganan' => $tipeLangganan,
        ]);

        return redirect()->route('members.index')->with('success', 'Member berhasil ditambahkan! Tier: ' . ucfirst($tipeLangganan));
    }

    public function show($id)
    {
        $member = Member::where('member_id', $id)->firstOrFail();
        return view('Member.show', compact('member'));
    }

    public function edit($id)
    {
        $member = Member::where('member_id', $id)->firstOrFail();
        return view('Member.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20|unique:members,no_hp,' . $id . ',member_id',
            'poin' => 'nullable|integer|min:0|max:600',
        ]);

        $member = Member::where('member_id', $id)->firstOrFail();

        \Log::info("=== DEBUG UPDATE MEMBER ===");
        \Log::info("Member: " . $member->member_id);
        \Log::info("Poin Lama: " . $member->poin);
        \Log::info("Tier Lama: " . $member->tipe_langganan);
        \Log::info("Poin Request: " . ($request->poin ?? 0));

        $oldTier = $member->tipe_langganan;
        $newPoin = $request->poin ?? 0;

        $newTier = Member::getTierByPoin($newPoin);
        \Log::info("Tier Baru yang Dihitung: " . $newTier);

        $updateData = [
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'poin' => $newPoin,
            'tipe_langganan' => $newTier,
        ];

        \Log::info("Data untuk update: " . json_encode($updateData));

        $member->update($updateData);


        $member->refresh();

        \Log::info("Tier Setelah Update: " . $member->tipe_langganan);
        \Log::info("Poin Setelah Update: " . $member->poin);
        \Log::info("Poin Raw (database): " . $member->getRawOriginal('poin'));

        $message = 'Data member berhasil diperbarui!';
        if ($oldTier !== $newTier) {
            $message .= ' Tier berubah: ' . $member->getLabelLangganan();
        }

        return redirect()->route('members.index')->with('success', $message);
    }
    public function destroy($id)
    {
        \DB::beginTransaction();

        try {
            $member = Member::where('member_id', $id)->firstOrFail();


            $member->poinHistory()->delete();
            $member->transaksi()->delete();

            $member->delete();

            \DB::commit();

            return redirect()->route('members.index')
                ->with('success', ' Member berhasil dihapus!');

        } catch (\Exception $e) {
            \DB::rollBack();

            return back()->with(
                'error',
                ' Gagal menghapus member: ' . $e->getMessage()
            );
        }

    }
}