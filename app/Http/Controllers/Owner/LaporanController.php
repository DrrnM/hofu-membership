<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Transaksi;
use App\Models\Member;
use App\Models\Poin;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    /**
     * ✅ METHOD INDEX - MENAMPILKAN DAFTAR LAPORAN
     */
    public function index(Request $request)
    {
        $query = Laporan::query();

        if ($request->filled('periode_laporan')) {
            $query->where('periode_laporan', 'like', '%' . $request->periode_laporan . '%');
        }

        if ($request->filled('tanggal_dibuat')) {
            $query->whereDate('tanggal_laporan', $request->tanggal_dibuat);
        }

        $laporans = $query->latest('tanggal_laporan')->get(); // ✅ GUNAKAN $laporans (PLURAL)
        $totalSemuaTransaksi = $laporans->sum('total_transaksi');

        return view('Owner.Laporan.index', compact('laporans', 'totalSemuaTransaksi'));
    }

    public function create()
    {
        return view('Owner.Laporan.create');
    }

    /**
     * ✅ METHOD STORE - PROSES UPLOAD FILE
     */
    public function store(Request $request)
    {
        \Log::info('=== START UPLOAD LAPORAN ===');
        \Log::info('Request data:', $request->all());

        try {
            $request->validate([
                'judul_laporan' => 'required|string|max:255',
                'file_laporan' => 'required|file|mimes:csv,xlsx,xls|max:10240'
            ]);

            \Log::info('✅ Validation passed');

            if ($request->hasFile('file_laporan')) {
                $file = $request->file('file_laporan');
                \Log::info('✅ File detected:', [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension()
                ]);

                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('laporan_files', $fileName, 'public');
                \Log::info('✅ File stored at: ' . $filePath);

                // ✅ PROSES DATA
                \Log::info('Starting Excel processing...');
                $result = $this->processExcelData($filePath);
                \Log::info('Excel processing result:', $result);

                $periodeLaporan = date('F Y');

                // ✅ BUAT LAPORAN DENGAN FIELD YANG SESUAI
                \Log::info('Creating Laporan record...');

                $laporanData = [
                    'judul_laporan' => $request->judul_laporan, // ✅ huruf kecil
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'total_transaksi' => $result['total_transaksi'],
                    'total_data' => $result['total_data'] ?? 0, // ✅ default value
                    'tanggal_laporan' => now(),
                    'periode_laporan' => $periodeLaporan,
                    'keterangan' => 'Laporan dari import file: ' . $file->getClientOriginalName()
                ];

                \Log::info('Data untuk create Laporan:', $laporanData);

                $laporan = Laporan::create($laporanData);
                \Log::info('✅ Laporan created with ID: ' . $laporan->id);

                \Log::info('=== UPLOAD SUCCESS ===');
                return redirect()->route('owner.laporan.index')
                    ->with(
                        'success',
                        'File berhasil diupload! ' .
                        $result['total_data'] . ' transaksi diproses. ' .
                        'Total: Rp ' . number_format($result['total_transaksi'])
                    );
            }

            \Log::warning('❌ No file uploaded');
            return back()->with('error', 'Tidak ada file yang diupload.');

        } catch (\Exception $e) {
            \Log::error('❌ Upload error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Gagal upload file: ' . $e->getMessage());
        }
    }

    /**
     * ✅ METHOD PROCESS EXCEL DATA
     */
    private function processExcelData($filePath)
    {
        $totalTransaksi = 0;
        $totalData = 0;

        try {
            \Log::info('=== PROCESS EXCEL DATA START ===');
            \Log::info('File path: ' . $filePath);

            $fileFullPath = Storage::disk('public')->path($filePath);
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

            if ($extension === 'csv') {
                // ✅ PROCESS CSV FILE
                \Log::info('Processing as CSV file');
                $result = $this->processCsvFile($fileFullPath);
                return $result;
            } else {
                // PROCESS EXCEL FILE
                $dummyImport = new class {};
                $data = Excel::toArray($dummyImport, $fileFullPath);

                if (!empty($data[0])) {
                    $rows = $data[0];
                    \Log::info('Total rows in Excel: ' . count($rows));

                    for ($i = 1; $i < count($rows); $i++) {
                        $row = $rows[$i];
                        \Log::info('Processing Excel row ' . $i . ': ' . json_encode($row));

                        if (!empty($row[0]) && !empty($row[1])) {
                            $idMember = trim($row[0]);
                            $totalBelanja = (float) $row[1];
                            $tanggal = $row[2] ?? now();

                            if ($this->processSingleTransaction($idMember, $totalBelanja, $tanggal)) {
                                $totalTransaksi += $totalBelanja;
                                $totalData++;
                            }
                        }
                    }
                }
            }

            \Log::info('=== PROCESS EXCEL DATA COMPLETED ===');
            \Log::info('Total Data: ' . $totalData . ', Total Transaksi: ' . $totalTransaksi);

            return [
                'total_transaksi' => $totalTransaksi,
                'total_data' => $totalData
            ];

        } catch (\Exception $e) {
            \Log::error('Error in processExcelData: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return [
                'total_transaksi' => 0,
                'total_data' => 0
            ];
        }
    }

    /**
     * ✅ METHOD UNTUK PROCESS CSV FILE
     */
    private function processCsvFile($filePath)
    {
        $totalTransaksi = 0;
        $totalData = 0;

        try {
            \Log::info('=== PROCESS CSV START ===');
            \Log::info('CSV File path: ' . $filePath);

            if (($handle = fopen($filePath, 'r')) !== FALSE) {
                $row = 0;

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    \Log::info('CSV Raw row ' . $row . ': ' . json_encode($data));

                    if ($row == 0) {
                        \Log::info('CSV Header: ' . json_encode($data));
                        $row++;
                        continue;
                    }

                    if (!empty($data[0]) && !empty($data[1])) {
                        $idMember = trim($data[0]);
                        $totalBelanja = (float) $data[1];

                        // ✅ FIX TANGGAL - FORMAT DD/MM/YYYY
                        $tanggal = now(); // default
                        if (!empty($data[2])) {
                            $tanggalInput = trim($data[2]);
                            \Log::info('Tanggal from CSV: ' . $tanggalInput);

                            // Coba format DD/MM/YYYY
                            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $tanggalInput, $matches)) {
                                $day = $matches[1];
                                $month = $matches[2];
                                $year = $matches[3];
                                $tanggal = $year . '-' . $month . '-' . $day . ' 00:00:00';
                                \Log::info('✅ Tanggal converted: ' . $tanggal);
                            }
                        }

                        \Log::info('Processing: Member=' . $idMember . ', Total=' . $totalBelanja . ', Tanggal=' . $tanggal);

                        // ✅ INI YANG PERLU DITAMBAH: PANGGIL processSingleTransaction
                        if ($this->processSingleTransaction($idMember, $totalBelanja, $tanggal)) {
                            $totalTransaksi += $totalBelanja;
                            $totalData++;
                            \Log::info('✅ Transaction SUCCESS: ' . $idMember . ' - Rp ' . $totalBelanja);
                        } else {
                            \Log::error('❌ Transaction FAILED for member: ' . $idMember);
                        }
                    } else {
                        \Log::warning('Skipping row ' . $row . ' - missing required data');
                    }

                    $row++;
                }
                fclose($handle);
            } else {
                \Log::error('Cannot open CSV file: ' . $filePath);
            }

            \Log::info('=== PROCESS CSV COMPLETED ===');
            \Log::info('Total CSV Data: ' . $totalData . ', Total Transaksi: ' . $totalTransaksi);

            return [
                'total_transaksi' => $totalTransaksi,
                'total_data' => $totalData
            ];

        } catch (\Exception $e) {
            \Log::error('Error in processCsvFile: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return [
                'total_transaksi' => 0,
                'total_data' => 0
            ];
        }
    }
    /**
     * ✅ METHOD PROCESS SINGLE TRANSACTION
     */
    private function processSingleTransaction($idMember, $totalBelanja, $tanggal)
    {
        \Log::info('=== 🚨 PROCESS SINGLE TRANSACTION START ===');
        \Log::info('Parameters - Member: ' . $idMember . ', Amount: ' . $totalBelanja . ', Date: ' . $tanggal);

        try {
            // ✅ BUAT TRANSAKSI LANGSUNG - TANPA CEK MEMBER DULU
            \Log::info('Creating transaction directly...');
            $transaksiData = [
                'member_id' => $idMember,
                'total_pembelian' => $totalBelanja,
                'jumlah_poin' => floor($totalBelanja / 10000),
                'created_at' => $tanggal,
                'updated_at' => $tanggal
            ];
            \Log::info('Transaction data: ' . json_encode($transaksiData));

            $transaksi = Transaksi::create($transaksiData);
            \Log::info('✅ TRANSACTION CREATED - ID: ' . $transaksi->id);

            \Log::info('=== ✅ TRANSACTION SUCCESS ===');
            return true;

        } catch (\Exception $e) {
            \Log::error('❌ TRANSACTION ERROR: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    public function download($id)
    {
        $laporan = Laporan::findOrFail($id);

        if (Storage::disk('public')->exists($laporan->file_path)) {
            $filePath = Storage::disk('public')->path($laporan->file_path);
            return response()->download($filePath, $laporan->file_name);
        }

        return back()->with('error', 'File tidak ditemukan.');
    }

    public function preview($id)
    {
        $laporan = Laporan::findOrFail($id);

        $extension = pathinfo($laporan->file_name, PATHINFO_EXTENSION);
        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            return back()->with('error', 'Hanya file Excel yang bisa dipreview.');
        }

        return back()->with('info', 'Fitur preview Excel membutuhkan package tambahan.');
    }
    public function destroy($id)
{
    $laporan = Laporan::findOrFail($id);

    try {
        \Log::info('=== DELETE LAPORAN START ===');
        \Log::info('Laporan to delete: ' . $laporan->judul_laporan);
        \Log::info('Periode: ' . $laporan->periode_laporan);
        \Log::info('Tanggal: ' . $laporan->tanggal_laporan);

        $deletedTransactions = Transaksi::whereDate('created_at', $laporan->tanggal_laporan->format('Y-m-d'))
                                       ->delete();
        
        \Log::info('Deleted transactions: ' . $deletedTransactions);

        if (Storage::disk('public')->exists($laporan->file_path)) {
            Storage::disk('public')->delete($laporan->file_path);
            \Log::info('File deleted from storage');
        }

        $laporan->delete();
        \Log::info('✅ Laporan deleted successfully');

        return redirect()->route('owner.laporan.index')
            ->with('success', 'Laporan dan ' . $deletedTransactions . ' transaksi berhasil dihapus!');

    } catch (\Exception $e) {
        \Log::error('❌ Delete error: ' . $e->getMessage());
        return back()->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
    }
}
    public function scanExistingFiles()
    {
        $existingFiles = Storage::disk('public')->files('laporan_files');

        $count = 0;
        foreach ($existingFiles as $file) {
            $fileName = basename($file);
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);

            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                $existingRecord = Laporan::where('file_path', $file)->first();

                if (!$existingRecord) {
                    $result = $this->processExcelData($file);

                    Laporan::create([
                        'Judul_laporan' => 'Laporan Existing - ' . $fileName,
                        'file_name' => $fileName,
                        'file_path' => $file,
                        'total_transaksi' => $result['total_transaksi'],
                        'total_data' => $result['total_data'],
                        'tanggal_laporan' => now(),
                        'periode_laporan' => date('F Y'),
                        'keterangan' => 'Laporan dari file existing: ' . $fileName
                    ]);
                    $count++;
                }
            }
        }

        return redirect()->route('owner.laporan.index')
            ->with('success', $count . ' file Excel berhasil di-scan dan diproses!');
    }


    private function isValidExcelFile($file)
    {
        $allowedExtensions = ['xlsx', 'xls', 'csv'];
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        return in_array($extension, $allowedExtensions);
    }
}