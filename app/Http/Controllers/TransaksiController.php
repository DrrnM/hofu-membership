<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Member;
use App\Models\Poin;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{

    public function create()
    {
        $members = Member::orderBy('nama')->get();
        return view('transactions.create', compact('members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_member' => 'required|exists:members,id_member',
            'total_belanja' => 'required|numeric|min:0',

        ]);

        try {
            $member = Member::where('id_member', $request->id_member)->firstOrFail();

            $transaksi = Transaksi::create([
                'id_member' => $request->id_member,
                'total_belanja' => $request->total_belanja,
                'tanggal_transaksi' => now()
            ]);

            $poinBertambah = floor($request->total_belanja / 10000);

            if ($poinBertambah > 0) {
                $poinSebelum = $member->poin;
                $member->poin += $poinBertambah;
                $member->updateTierOtomatis();
                $member->save();

                Poin::create([
                    'member_id' => $request->id_member,
                    'jumlah_poin' => $poinBertambah,
                    'keterangan' => 'Poin dari transaksi Rp ' . number_format($request->total_belanja),
                    'created_at' => now()
                ]);

                $message = "Transaksi berhasil! Poin bertambah: {$poinBertambah} poin";
                $message .= " ({$poinSebelum} → {$member->poin})";
            } else {
                $message = "Transaksi berhasil! Belanja kurang dari Rp 10.000, tidak dapat poin";
            }

            return redirect()->route('transactions.today')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'id_member' => 'required|exists:members,id_member',
            'total_belanja' => 'required|numeric|min:0'
        ]);

        try {
            $member = Member::where('id_member', $request->id_member)->firstOrFail();

            $transaksi = Transaksi::create([
                'id_member' => $request->id_member,
                'total_belanja' => $request->total_belanja,
                'tanggal_transaksi' => now(),
            ]);

            $poinBertambah = floor($request->total_belanja / 10000);

            if ($poinBertambah > 0) {
                $member->poin += $poinBertambah;
                $member->updateTierOtomatis();
                $member->save();

                Poin::create([
                    'member_id' => $request->id_member,
                    'jumlah_poin' => $poinBertambah,
                    'keterangan' => 'Poin dari transaksi cepat Rp ' . number_format($request->total_belanja),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $poinBertambah > 0 ?
                    "Transaksi berhasil! +{$poinBertambah} poin" :
                    "Transaksi berhasil!",
                'poin_bertambah' => $poinBertambah,
                'poin_sekarang' => $member->poin
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }
}