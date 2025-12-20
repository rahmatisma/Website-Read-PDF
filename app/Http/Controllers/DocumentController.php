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
    /**
 * ✅ HAPUS DOKUMEN - AUTO CASCADE DELETE dengan PENGECEKAN JARINGAN
 * 
 * Method ini akan menghapus:
 * 1. File fisik dari storage
 * 2. Record upload dari database
 * 3. SPK terkait (via database CASCADE)
 * 4. Semua tabel child SPK (via database CASCADE)
 * 5. JARINGAN (HANYA jika tidak ada SPK lain yang menggunakan)
 */
    public function destroy($id)
    {
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
            
            // Ambil semua SPK terkait dengan dokumen ini
            $spks = SPK::where('id_upload', $upload->id_upload)->get();
            
            // Kumpulkan semua no_jaringan yang terkait
            $noJaringanList = [];
            
            foreach ($spks as $spk) {
                Log::info('Ditemukan SPK terkait', [
                    'id_spk' => $spk->id_spk,
                    'no_spk' => $spk->no_spk,
                    'no_jaringan' => $spk->no_jaringan
                ]);
                
                // Simpan no_jaringan untuk dihapus nanti (hindari duplikat)
                if (!in_array($spk->no_jaringan, $noJaringanList)) {
                    $noJaringanList[] = $spk->no_jaringan;
                }
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
            
            // Hapus dokumen (ini akan trigger cascade delete ke SPK dan child tables)
            $upload->delete();
            
            // Setelah SPK terhapus, cek dulu sebelum hapus JARINGAN
            if (!empty($noJaringanList)) {
                $deletedJaringanCount = 0;
                $skippedJaringanCount = 0;
                
                foreach ($noJaringanList as $noJaringan) {
                    // CEK: Apakah masih ada SPK lain yang pakai no_jaringan ini?
                    $masihAdaSPK = SPK::where('no_jaringan', $noJaringan)->exists();
                    
                    // HANYA hapus JARINGAN jika tidak ada SPK yang pakai lagi
                    if (!$masihAdaSPK) {
                        DB::table('JARINGAN')
                            ->where('no_jaringan', $noJaringan)
                            ->delete();
                        
                        $deletedJaringanCount++;
                        
                        Log::info('JARINGAN berhasil dihapus (tidak ada SPK lagi)', [
                            'no_jaringan' => $noJaringan
                        ]);
                    } else {
                        $skippedJaringanCount++;
                        
                        Log::info('JARINGAN TIDAK dihapus (masih ada SPK lain)', [
                            'no_jaringan' => $noJaringan
                        ]);
                    }
                }
                
                Log::info('Ringkasan penghapusan JARINGAN', [
                    'total_jaringan_ditemukan' => count($noJaringanList),
                    'jaringan_terhapus' => $deletedJaringanCount,
                    'jaringan_dilewati' => $skippedJaringanCount
                ]);
            }
            
            // Commit transaction
            DB::commit();
            
            Log::info('Dokumen, SPK, dan JARINGAN berhasil dihapus', [
                'id_upload' => $id,
                'jumlah_spk' => count($spks),
                'jumlah_jaringan_dicek' => count($noJaringanList),
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