<?php
// Membaca document yang sudah ada untuk di tampilkan di dashboard
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\SPK;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * Menampilkan semua dokumen user
     */
    public function index()
    {
        $documents = Document::where('id_user', Auth::id())->get();

        return Inertia::render('Documents/Index', [
            'documents' => $documents,
            'activeTab' => 'index', // tanda khusus utk "semua dokumen"
        ]);
    }

    /**
     * Filter dokumen berdasarkan tipe
     */
    public function filter(string $type)
    {
        $query = Document::where('id_user', Auth::id());

        switch ($type) {
            case 'pdf':
                $query->where('file_type', 'pdf');
                break;
            case 'doc':
                $query->whereIn('file_type', ['doc', 'docx']);
                break;
            case 'gambar':
                $query->whereIn('file_type', ['jpg', 'jpeg', 'png']);
                break;
            default:
                abort(404);
        }

        $documents = $query->get();

        return Inertia::render('Documents/Index', [
            'documents' => $documents,
            'activeTab' => $type,
        ]);
    }

    /**
     * Kirim file ke Python untuk diproses
     */
    public function sendToPython(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'File tidak ditemukan'], 400);
        }

        $file = $request->file('file');

        $client = new Client();

        try {
            $response = $client->post('http://127.0.0.1:5000/process-pdf', [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen($file->getPathname(), 'r'),
                        'filename' => $file->getClientOriginalName(),
                    ],
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return response()->json([
                'message' => 'Berhasil diproses oleh Python',
                'output_file' => $result['output_file'],
                'data' => $result['data'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Dashboard - menampilkan statistik dokumen
     */
    public function dashboard()
    {
        $userId = Auth::id();

        $countPDF = Document::where('id_user', $userId)
            ->where('file_type', 'pdf')
            ->count();

        $countDOC = Document::where('id_user', $userId)
            ->whereIn('file_type', ['doc', 'docx'])
            ->count();

        $countIMG = Document::where('id_user', $userId)
            ->whereIn('file_type', ['jpg', 'jpeg', 'png'])
            ->count();

        $countAll = Document::where('id_user', $userId)->count();

        return Inertia::render('dashboard', [
            'countPDF'  => $countPDF,
            'countDOC'  => $countDOC,
            'countIMG'  => $countIMG,
            'countAll'  => $countAll,
        ]);
    }
    
    /**
     * ✅ HAPUS DOKUMEN - AUTO CASCADE DELETE
     * 
     * Method ini akan menghapus:
     * 1. File fisik dari storage
     * 2. Record upload dari database
     * 3. SPK terkait (via database CASCADE)
     * 4. Semua tabel child SPK (via database CASCADE)
     */
    public function destroy($id)
    {
        // Gunakan DB Transaction untuk safety
        DB::beginTransaction();
        
        try {
            // Cari dokumen berdasarkan ID
            $upload = Document::findOrFail($id);
            
            // Validasi: Pastikan user hanya bisa hapus dokumen miliknya sendiri
            if ($upload->id_user !== Auth::id()) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus dokumen ini! ❌');
            }
            
            // Log untuk debugging
            Log::info('Memulai proses penghapusan dokumen', [
                'id_upload' => $upload->id_upload,
                'file_name' => $upload->file_name,
                'file_path' => $upload->file_path,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);
            
            // Cek apakah ada SPK terkait (untuk logging saja)
            $spk = SPK::where('id_upload', $upload->id_upload)->first();
            if ($spk) {
                Log::info('Ditemukan SPK terkait yang akan ikut terhapus', [
                    'id_spk' => $spk->id_spk,
                    'no_spk' => $spk->no_spk,
                    'no_jaringan' => $spk->no_jaringan
                ]);
            }
            
            // Hapus file fisik dari storage
            if (Storage::exists('public/' . $upload->file_path)) {
                Storage::delete('public/' . $upload->file_path);
                Log::info('File fisik berhasil dihapus dari storage', [
                    'path' => $upload->file_path
                ]);
            } else {
                Log::warning('File fisik tidak ditemukan di storage', [
                    'path' => $upload->file_path
                ]);
            }
            
            // ✅ KUNCI UTAMA: Hapus upload dari database
            // Database CASCADE akan otomatis menghapus:
            // 1. SPK (karena FK id_upload ON DELETE CASCADE)
            // 2. SPK_Pelaksanaan (karena FK id_spk ON DELETE CASCADE)
            // 3. SPK_Execution_Info (karena FK id_spk ON DELETE CASCADE)
            // 4. SPK_Informasi_Gedung (karena FK id_spk ON DELETE CASCADE)
            // 5. SPK_Sarpen_Ruang_Server (karena FK id_spk ON DELETE CASCADE)
            //    └─> SPK_Sarpen_Tegangan (karena FK id_sarpen ON DELETE CASCADE)
            // 6. SPK_Lokasi_Antena (karena FK id_spk ON DELETE CASCADE)
            // 7. SPK_Perizinan_Biaya_Gedung (karena FK id_spk ON DELETE CASCADE)
            // 8. SPK_Penempatan_Perangkat (karena FK id_spk ON DELETE CASCADE)
            // 9. SPK_Perizinan_Biaya_Kawasan (karena FK id_spk ON DELETE CASCADE)
            // 10. SPK_Kawasan_Umum (karena FK id_spk ON DELETE CASCADE)
            // 11. SPK_Data_Splitter (karena FK id_spk ON DELETE CASCADE)
            // 12. SPK_HH_Eksisting (karena FK id_spk ON DELETE CASCADE)
            // 13. SPK_HH_Baru (karena FK id_spk ON DELETE CASCADE)
            // 14. Dokumentasi_Foto (karena FK id_spk ON DELETE CASCADE)
            // 15. Berita_Acara (karena FK id_spk ON DELETE CASCADE)
            // 16. List_Item (karena FK id_spk ON DELETE CASCADE)
            // 17. Form_Checklist_Wireline (karena FK id_spk ON DELETE CASCADE)
            //     └─> FCW_Waktu_Pelaksanaan (karena FK id_fcw ON DELETE CASCADE)
            //     └─> FCW_Tegangan (karena FK id_fcw ON DELETE CASCADE)
            //     └─> FCW_Data_Perangkat (karena FK id_fcw ON DELETE CASCADE)
            //     └─> FCW_Line_Checklist (karena FK id_fcw ON DELETE CASCADE)
            //     └─> FCW_Guidance_Foto (karena FK id_fcw ON DELETE CASCADE)
            //     └─> FCW_Log (karena FK id_fcw ON DELETE CASCADE)
            // 18. Form_Checklist_Wireless (karena FK id_spk ON DELETE CASCADE)
            //     └─> FCWL_Waktu_Pelaksanaan (karena FK id_fcwl ON DELETE CASCADE)
            //     └─> FCWL_Indoor_Area (karena FK id_fcwl ON DELETE CASCADE)
            //     └─> FCWL_Outdoor_Area (karena FK id_fcwl ON DELETE CASCADE)
            //     └─> FCWL_Perangkat_Antenna (karena FK id_fcwl ON DELETE CASCADE)
            //     └─> FCWL_Cabling_Installation (karena FK id_fcwl ON DELETE CASCADE)
            //     └─> FCWL_Data_Perangkat (karena FK id_fcwl ON DELETE CASCADE)
            //     └─> FCWL_Guidance_Foto (karena FK id_fcwl ON DELETE CASCADE)
            //     └─> FCWL_Log (karena FK id_fcwl ON DELETE CASCADE)
            
            $upload->delete();
            
            // Commit transaction
            DB::commit();
            
            Log::info('Dokumen dan semua data terkait berhasil dihapus via CASCADE', [
                'id_upload' => $id,
                'success' => true
            ]);
            
            return redirect()->back()->with('success', 'Dokumen dan semua data terkait berhasil dihapus! ✅');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            
            Log::error('Dokumen tidak ditemukan', [
                'id_upload' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan! ❌');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Gagal menghapus dokumen', [
                'id_upload' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage() . ' ❌');
        }
    }
}