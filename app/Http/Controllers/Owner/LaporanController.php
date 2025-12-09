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
        $tempFilePath = null; // ⬅️ TAMBAHKAN: File sementara

        try {
            // 1. VALIDASI INPUT DASAR
            $request->validate([
                'judul_laporan' => 'required|string|max:255',
                'file_laporan' => 'required|file|mimes:csv,txt|max:10240'
            ]);

            if (!$request->hasFile('file_laporan')) {
                return back()->with('error', 'Tidak ada file yang diupload.');
            }

            $file = $request->file('file_laporan');

            // 2. VALIDASI EKSTENSI
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['csv', 'txt'];

            if (!in_array($extension, $allowedExtensions)) {
                return back()->with('error', 'Hanya file CSV/TXT yang diperbolehkan.');
            }

            // 3. VALIDASI UKURAN FILE & KONTEN SEBELUM SIMPAN
            $fileSize = $file->getSize();
            if ($fileSize == 0) {
                return back()->with('error', 'File kosong (0 byte).');
            }

            // Baca sample konten untuk validasi awal
            $fileContent = file_get_contents($file->getRealPath());
            if (empty(trim($fileContent))) {
                return back()->with('error', 'File tidak berisi data.');
            }

            // 4. SIMPAN KE TEMP FOLDER DULU (bukan langsung ke public)
            $tempFileName = 'temp_' . time() . '_' . $file->getClientOriginalName();
            $tempFilePath = $file->storeAs('temp_laporan', $tempFileName, 'local'); // ⬅️ Simpan di storage/app/temp_laporan

            \Log::info("File disimpan sementara: {$tempFilePath}");

            // 5. VALIDASI FILE CSV (baca isinya)
            $validationResult = $this->validateCSVFile($tempFilePath); // ⬅️ BUAT METHOD INI

            if (!$validationResult['valid']) {
                throw new \Exception('File CSV tidak valid: ' . $validationResult['message']);
            }

            // 6. JIKA VALIDASI BERHASIL, PINDAHKAN KE FOLDER PUBLIC
            $finalFileName = time() . '_' . $file->getClientOriginalName();
            $finalFilePath = 'laporan_files/' . $finalFileName;

            // Pindahkan dari temp ke public
            Storage::disk('public')->put(
                $finalFilePath,
                Storage::disk('local')->get($tempFilePath)
            );

            // Hapus file temp
            Storage::disk('local')->delete($tempFilePath);

            $filePath = $finalFilePath; // ⬅️ File final di public

            // 7. PROSES CSV
            $result = $this->processCSV($filePath);

            if (empty($result) || !isset($result['total_transaksi'])) {
                throw new \Exception('Gagal memproses file CSV.');
            }

            $periodeLaporan = date('F Y');

            // 8. SIMPAN KE DATABASE
            $laporanData = [
                'judul_laporan' => $request->judul_laporan,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'total_transaksi' => $result['total_transaksi'] ?? 0,
                'total_data' => $result['total_data'] ?? 0,
                'tanggal_laporan' => now(),
                'periode_laporan' => $periodeLaporan,
                'keterangan' => 'Laporan CSV: ' . $file->getClientOriginalName()
            ];

            $laporan = Laporan::create($laporanData);

            \DB::commit();

            // 9. RESPONSE SUCCESS
            $flashType = 'success';
            $flashMessage = ' File CSV berhasil diupload!' .
                '📊 ' . ($result['total_data'] ?? 0) . ' transaksi diproses.' .
                '💰 Total: Rp ' . number_format($result['total_transaksi'] ?? 0, 0, ',', '.');

            if (!empty($result['errors']) && ($result['total_data'] ?? 0) > 0) {
                $flashType = 'warning';
                $errorSummary = $this->getErrorSummary(
                    $result['errors'],
                    count($result['errors']) + ($result['total_data'] ?? 0)
                );
                $flashMessage = $errorSummary;
            } elseif (!empty($result['errors'])) {
                $errorSummary = $this->getErrorSummary(
                    $result['errors'],
                    count($result['errors'])
                );
                throw new \Exception($errorSummary);
            }

            \Log::info('=== UPLOAD BERHASIL ===');
            return redirect()->route('owner.laporan.index')
                ->with($flashType, $flashMessage);

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Upload CSV error: ' . $e->getMessage());

            // 10. CLEANUP JIKA ERROR
            // Hapus file temp jika ada
            if ($tempFilePath !== null && Storage::disk('local')->exists($tempFilePath)) {
                Storage::disk('local')->delete($tempFilePath);
                \Log::info("File temp dihapus: {$tempFilePath}");
            }

            // Hapus file final jika sudah terupload ke public
            if ($filePath !== null && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                \Log::info("File public dihapus: {$filePath}");
            }

            $errorMessage = ' Gagal upload file CSV: ' . $e->getMessage();

            if ($file !== null) {
                $errorMessage .= '📄 File: ' . $file->getClientOriginalName();
            }

            return back()
                ->with('error', $errorMessage)
                ->withInput(); // ⬅️ Keep form input
        }
    }

    private function validateCSVFile($filePath)
    {
        try {
            $fullPath = Storage::disk('local')->path($filePath);

            // Cek file exists
            if (!file_exists($fullPath)) {
                return ['valid' => false, 'message' => 'File tidak ditemukan'];
            }

            // Cek file size
            $fileSize = filesize($fullPath);
            if ($fileSize == 0) {
                return ['valid' => false, 'message' => 'File kosong'];
            }

            // Baca beberapa baris pertama untuk validasi format
            $file = fopen($fullPath, 'r');
            if (!$file) {
                return ['valid' => false, 'message' => 'Tidak bisa membaca file'];
            }

            // Baca header (baris pertama)
            $header = fgetcsv($file);
            if ($header === false) {
                fclose($file);
                return ['valid' => false, 'message' => 'File tidak memiliki header'];
            }

            // Cek minimal kolom (contoh: harus ada id, tanggal, jumlah)
            $minColumns = 3;
            if (count($header) < $minColumns) {
                fclose($file);
                return ['valid' => false, 'message' => 'Format CSV tidak sesuai. Minimal ' . $minColumns . ' kolom'];
            }

            // Cek apakah ada data selain header
            $firstData = fgetcsv($file);
            if ($firstData === false) {
                fclose($file);
                return ['valid' => false, 'message' => 'File hanya berisi header, tidak ada data'];
            }

            fclose($file);

            return ['valid' => true, 'message' => 'File valid'];

        } catch (\Exception $e) {
            return ['valid' => false, 'message' => 'Error validasi: ' . $e->getMessage()];
        }
    }

    public function cleanupTempFiles()
    {
        try {
            $files = Storage::disk('local')->files('temp_laporan');
            $deletedCount = 0;

            foreach ($files as $file) {
                // Hapus file temp yang lebih dari 1 jam
                $lastModified = Storage::disk('local')->lastModified($file);
                if (time() - $lastModified > 3600) { // 1 jam
                    Storage::disk('local')->delete($file);
                    $deletedCount++;
                    \Log::info("Cleanup temp file: {$file}");
                }
            }

            return "Deleted {$deletedCount} temp files";

        } catch (\Exception $e) {
            \Log::error('Cleanup error: ' . $e->getMessage());
            return "Cleanup failed: " . $e->getMessage();
        }
    }

    public function download($id)
    {
        $laporan = Laporan::findOrFail($id);

        $filePath = storage_path('app/public/' . $laporan->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download($filePath, $laporan->file_name);
    }
    public function destroy($id)
    {
        \DB::beginTransaction();

        try {
            $laporan = Laporan::findOrFail($id);

            \Log::info("Menghapus laporan CSV ID: {$id}");

            if (Storage::disk('public')->exists($laporan->file_path)) {
                Storage::disk('public')->delete($laporan->file_path);
            }
            $laporan->delete();

            \DB::commit();

            return redirect()->route('owner.laporan.index')
                ->with('success', ' Laporan CSV berhasil dihapus!');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Gagal hapus laporan: " . $e->getMessage());

            return back()->with('error', ' Gagal menghapus laporan: ' . $e->getMessage());
        }
    }

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