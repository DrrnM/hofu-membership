<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Laporan::query();

        // Filter berdasarkan periode atau tanggal
        if ($request->filled('periode_laporan')) {
            $query->where('periode_laporan', 'like', '%' . $request->periode_laporan . '%');
        }

        if ($request->filled('tanggal_dibuat')) {
            $query->whereDate('tanggal_dibuat', $request->tanggal_dibuat);
        }

        $laporans = $query->latest('tanggal_dibuat')->get();

        return view('Owner.Laporan.index', compact('laporans'));
    }

    public function destroy($id)
    {
        $laporan = Laporan::findOrFail($id);
        $laporan->delete();
        return redirect()->route('owner.laporan.index')->with('success', 'Laporan berhasil dihapus!');
    }
}
