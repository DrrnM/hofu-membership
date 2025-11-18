<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Laporan::query();

        if ($request->filled('periode_laporan')) {
            $query->where('periode_laporan', 'like', '%' . $request->periode_laporan . '%');
        }
     
        if ($request->filled('tanggal_dibuat')) {
            $query->whereDate('tanggal_laporan', $request->tanggal_dibuat);
        }

        $laporans = $query->latest('tanggal_laporan')->get();
        $totalSemuaTransaksi = $laporans->sum('total_transaksi');

        return view('Owner.Laporan.index', compact('laporans', 'totalSemuaTransaksi'));
    }

    public function create()
    {
        return view('Owner.Laporan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_laporan' => 'required|file|mimes:csv,xlsx,xls|max:2048'
        ]);

        try {
            if ($request->hasFile('file_laporan')) {
                $file = $request->file('file_laporan');
                
                // Generate unique filename
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('laporan', $fileName, 'public');
                
                // Simpan ke database
                Laporan::create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'total_transaksi' => 0, // Default value
                    'tanggal_laporan' => now(),
                    'periode_laporan' => date('F Y') // Otomatis bulan tahun
                ]);

                return redirect()->route('owner.laporan.index')
                    ->with('success', 'File laporan berhasil diupload!');
            }

            return back()->with('error', 'Tidak ada file yang diupload.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal upload file: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $laporan = Laporan::findOrFail($id);
        
        // Hapus file dari storage
        Storage::disk('public')->delete($laporan->file_path);
        
        $laporan->delete();
        return redirect()->route('owner.laporan.index')->with('success', 'Laporan berhasil dihapus!');
    }
}