<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Transaksi;
use App\Models\Member;
use App\Models\Poin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Laporan::query();

        // Filter berdasarkan tanggal upload
        if ($request->filled('tanggal_dibuat')) {
            $query->whereDate('created_at', $request->tanggal_dibuat);
        }

        // Filter berdasarkan periode laporan
        if ($request->filled('periode_laporan')) {
            $query->where('periode_laporan', 'like', '%' . $request->periode_laporan . '%');
        }

        // Sort terbaru
        $query->orderBy('created_at', 'desc');

        $laporans = $query->paginate(20);

        // Hitung total semua transaksi
        $totalSemuaTransaksi = Laporan::sum('total_transaksi');

        return view('owner.laporan.index', compact('laporans', 'totalSemuaTransaksi'));
    }


    public function create()
    {
        return view('owner.laporan.create');
    }

    public function store(Request $request)
    {
        \Log::info('=== START UPLOAD LAPORAN CSV ===');

        \DB::beginTransaction();

        $file = null;
        $filePath = null;

        try {
            $request->validate([
                'judul_laporan' => 'required|string|max:255',
                'file_laporan' => 'required|file|mimes:csv|max:10240' // Hanya CSV
            ]);

            if (!$request->hasFile('file_laporan')) {
                return back()->with('error', 'Tidak ada file yang diupload.');
            }

            $file = $request->file('file_laporan');

            $extension = $file->getClientOriginalExtension();
            if (strtolower($extension) !== 'csv') {
                return back()->with('error', 'Hanya file CSV yang diperbolehkan.');
            }

            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('laporan_files', $fileName, 'public');


            $this->validateFileNotEmpty($filePath);

            $result = $this->processCSV($filePath);

            $periodeLaporan = date('F Y');

            $laporanData = [
                'judul_laporan' => $request->judul_laporan,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'total_transaksi' => $result['total_transaksi'],
                'total_data' => $result['total_data'] ?? 0,
                'tanggal_laporan' => now(),
                'periode_laporan' => $periodeLaporan,
                'keterangan' => 'Laporan CSV: ' . $file->getClientOriginalName()
            ];

            $laporan = Laporan::create($laporanData);

            \DB::commit();
            $flashType = 'success';
            $flashMessage = '✅ File CSV berhasil diupload!<br>' .
                '📊 ' . $result['total_data'] . ' transaksi diproses.<br>' .
                '💰 Total: Rp ' . number_format($result['total_transaksi'], 0, ',', '.');

            if (!empty($result['errors']) && $result['total_data'] > 0) {
                $flashType = 'warning';
                $errorSummary = $this->getErrorSummary($result['errors'], count($result['errors']) + $result['total_data']);
                $flashMessage = $errorSummary;
            }
            elseif (!empty($result['errors'])) {
                $errorSummary = $this->getErrorSummary($result['errors'], count($result['errors']));
                throw new \Exception($errorSummary);
            }

            return redirect()->route('owner.laporan.index')
                ->with($flashType, $flashMessage);

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Upload CSV error: ' . $e->getMessage());

            if ($filePath !== null && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            $errorMessage = '❌ Gagal upload file CSV: ' . $e->getMessage();

            if ($file !== null) {
                $errorMessage .= ' [File: ' . $file->getClientOriginalName() . ']';
            }

            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Download the specified file.
     */
    public function download($id)
    {
        $laporan = Laporan::findOrFail($id);

        $filePath = storage_path('app/public/' . $laporan->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download($filePath, $laporan->file_name);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        \DB::beginTransaction();

        try {
            $laporan = Laporan::findOrFail($id);

            \Log::info("Menghapus laporan CSV ID: {$id}");

            // Hapus file dari storage
            if (Storage::disk('public')->exists($laporan->file_path)) {
                Storage::disk('public')->delete($laporan->file_path);
            }

            // Hapus record laporan (tapi transaksi tetap di database)
            $laporan->delete();

            \DB::commit();

            return redirect()->route('owner.laporan.index')
                ->with('success', '✅ Laporan CSV berhasil dihapus! Data transaksi tetap tersimpan di database.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Gagal hapus laporan: " . $e->getMessage());

            return back()->with('error', '❌ Gagal menghapus laporan: ' . $e->getMessage());
        }
    }

    // ================= HELPER METHODS =================

    /**
     * Proses file CSV dan simpan transaksi ke database
     */
    private function processCSV($filePath)
    {
        $fullPath = storage_path('app/public/' . $filePath);

        if (!file_exists($fullPath)) {
            throw new \Exception('File tidak ditemukan: ' . $filePath);
        }

        $handle = fopen($fullPath, 'r');
        if (!$handle) {
            throw new \Exception('Tidak bisa membuka file CSV');
        }

        // Skip header (baris pertama)
        $header = fgetcsv($handle);
        \Log::info('CSV Header: ' . json_encode($header));

        $totalTransaksi = 0;
        $totalData = 0;
        $errors = [];

        $lineNumber = 1; // Mulai dari 1 karena sudah skip header

        while (($data = fgetcsv($handle)) !== false) {
            $lineNumber++;

            try {
                // Minimal harus ada 3 kolom: member_id, total_pembelian, tanggal
                if (count($data) < 3) {
                    $errors[] = "Baris $lineNumber: Format data tidak valid (harus ada 3 kolom)";
                    continue;
                }

                $memberId = trim($data[0]);
                $totalPembelian = $this->cleanNumber($data[1]);
                $tanggalStr = trim($data[2]);

                // Validasi data
                if (empty($memberId)) {
                    $errors[] = "Baris $lineNumber: ID Member kosong";
                    continue;
                }

                if ($totalPembelian <= 0) {
                    $errors[] = "Baris $lineNumber: Total pembelian tidak valid: " . $data[1];
                    continue;
                }

                // Parse tanggal
                try {
                    $tanggal = $this->parseTanggal($tanggalStr);
                } catch (\Exception $e) {
                    $errors[] = "Baris $lineNumber: Format tanggal tidak valid: " . $tanggalStr;
                    continue;
                }

                // Cek member exist
                $member = Member::where('member_id', $memberId)->first();
                if (!$member) {
                    $errors[] = "Baris $lineNumber: Member ID '$memberId' tidak ditemukan";
                    continue;
                }

                // Semua validasi lolos, tambahkan ke total
                $totalTransaksi += $totalPembelian;
                $totalData++;

                // ✅ SIMPAN TRANSAKSI KE DATABASE
                $transaksi = Transaksi::create([
                    'member_id' => $memberId,
                    'total_pembelian' => $totalPembelian,
                    'tanggal' => $tanggal
                ]);

                // ✅ UPDATE POIN MEMBER
                $transaksi->updateMemberPoints();

                \Log::info("✅ Transaksi dibuat - ID: {$transaksi->id_transaksi}, Member: {$memberId}, Total: Rp " . number_format($totalPembelian, 0, ',', '.'));

            } catch (\Exception $e) {
                $errors[] = "Baris $lineNumber: " . $e->getMessage();
                continue;
            }
        }

        fclose($handle);

        // Validasi jika tidak ada data yang berhasil diproses
        if ($totalData === 0 && empty($errors)) {
            throw new \Exception('File CSV tidak mengandung data transaksi.');
        }

        return [
            'total_transaksi' => $totalTransaksi,
            'total_data' => $totalData,
            'errors' => $errors
        ];
    }

    /**
     * Helper: Bersihkan angka dari format currency
     */
    private function cleanNumber($value)
    {
        $cleaned = str_replace(['Rp', ' ', '.', ','], '', trim($value));
        return (int) $cleaned;
    }

    /**
     * Helper: Parse tanggal dari berbagai format
     */
    private function parseTanggal($tanggalStr)
    {
        try {
            // Format d/m/Y (25/11/2025)
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $tanggalStr)) {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $tanggalStr);
            }

            // Format Y-m-d (2025-11-25)
            if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $tanggalStr)) {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalStr);
            }

            // Format d-m-Y (25-11-2025)
            if (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $tanggalStr)) {
                return \Carbon\Carbon::createFromFormat('d-m-Y', $tanggalStr);
            }

            // Default try Carbon parse
            return \Carbon\Carbon::parse($tanggalStr);

        } catch (\Exception $e) {
            throw new \Exception("Format tanggal tidak dikenali: $tanggalStr. Gunakan format: dd/mm/yyyy atau yyyy-mm-dd");
        }
    }

    /**
     * Validasi file tidak kosong
     */
    private function validateFileNotEmpty($filePath)
    {
        $fullPath = storage_path('app/public/' . $filePath);

        if (!file_exists($fullPath)) {
            throw new \Exception('File tidak ditemukan.');
        }

        $fileSize = filesize($fullPath);

        if ($fileSize === 0 || $fileSize === false) {
            throw new \Exception('File kosong atau tidak valid.');
        }

        // Cek minimal ada content
        $content = file_get_contents($fullPath, false, null, 0, 100);
        if (trim($content) === '') {
            throw new \Exception('File kosong (tidak ada konten).');
        }
    }

    /**
     * Format error summary
     */
    private function getErrorSummary($errors, $totalRows)
    {
        $errorCount = count($errors);
        $successCount = $totalRows - $errorCount;

        $summary = "⚠️ Terdapat $errorCount error dari $totalRows baris data:<br><br>";

        // Limit error display
        $displayErrors = array_slice($errors, 0, 10);
        foreach ($displayErrors as $error) {
            $summary .= "• $error<br>";
        }

        if ($errorCount > 10) {
            $summary .= "• ... dan " . ($errorCount - 10) . " error lainnya<br>";
        }

        if ($successCount > 0) {
            $summary .= "<br>✅ $successCount data berhasil diproses dan disimpan.";
        }

        return $summary;
    }
}