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
            'activeTab' => 'index',
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
     * ✅ HAPUS DOKUMEN - AUTO CASCADE DELETE dengan PENGECEKAN JARINGAN
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $upload = Document::findOrFail($id);
            
            if ($upload->id_user !== Auth::id()) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus dokumen ini! ❌');
            }
            
            Log::info('Memulai proses penghapusan dokumen', [
                'id_upload' => $upload->id_upload,
                'file_name' => $upload->file_name,
                'file_path' => $upload->file_path,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);
            
            $spks = SPK::where('id_upload', $upload->id_upload)->get();
            $noJaringanList = [];
            
            foreach ($spks as $spk) {
                Log::info('Ditemukan SPK terkait', [
                    'id_spk' => $spk->id_spk,
                    'no_spk' => $spk->no_spk,
                    'no_jaringan' => $spk->no_jaringan
                ]);
                
                if (!in_array($spk->no_jaringan, $noJaringanList)) {
                    $noJaringanList[] = $spk->no_jaringan;
                }
            }
            
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
            
            $upload->delete();
            
            if (!empty($noJaringanList)) {
                $deletedJaringanCount = 0;
                $skippedJaringanCount = 0;
                
                foreach ($noJaringanList as $noJaringan) {
                    $masihAdaSPK = SPK::where('no_jaringan', $noJaringan)->exists();
                    
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

    /**
     * ✅ DETAIL DOKUMEN
     */
    public function detail($id)
    {
        try {
            $upload = Document::findOrFail($id);
            
            if ($upload->id_user !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses ke dokumen ini');
            }
            
            Log::info('Mengakses detail dokumen', [
                'id_upload' => $upload->id_upload,
                'file_name' => $upload->file_name,
                'status' => $upload->status,
                'user_id' => Auth::id()
            ]);
            
            $extractedData = null;
            if ($upload->extracted_data) {
                $extractedData = is_string($upload->extracted_data) 
                    ? json_decode($upload->extracted_data, true) 
                    : $upload->extracted_data;
            }
            
            return Inertia::render('Documents/Detail', [
                'upload' => [
                    'id_upload' => $upload->id_upload,
                    'file_name' => $upload->file_name,
                    'file_path' => $upload->file_path,
                    'file_size' => $upload->file_size,
                    'document_type' => $upload->document_type,
                    'status' => $upload->status,
                    'created_at' => $upload->created_at,
                    'updated_at' => $upload->updated_at,
                ],
                'extractedData' => $extractedData,
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Dokumen tidak ditemukan', [
                'id_upload' => $id,
                'error' => $e->getMessage()
            ]);
            
            abort(404, 'Dokumen tidak ditemukan');
            
        } catch (\Exception $e) {
            Log::error('Gagal memuat detail dokumen', [
                'id_upload' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Terjadi kesalahan saat memuat detail dokumen');
        }
    }
    
    /**
     * ✅ GET STATUS SINGLE DOCUMENT (untuk polling individual)
     * GET /api/documents/{id}/status
     */
    public function getStatus($id)
    {
        $document = Document::where('id_upload', $id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$document) {
            return response()->json([
                'error' => 'Document not found'
            ], 404);
        }

        return response()->json([
            'id_upload' => $document->id_upload,
            'status' => $document->status,
            'file_name' => $document->file_name,
            'created_at' => $document->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $document->updated_at->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * ✅ CHECK STATUS MULTIPLE DOCUMENTS (untuk polling batch)
     * POST /api/documents/check-status
     * Body: { "ids": [1, 2, 3] }
     */
    public function checkStatus(Request $request)
    {
        $ids = $request->input('ids', []);
        
        // Validasi input
        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'error' => 'IDs harus berupa array dan tidak boleh kosong'
            ], 400);
        }
        
        // Ambil dokumen milik user yang sedang login saja
        $documents = Document::whereIn('id_upload', $ids)
            ->where('id_user', Auth::id())
            ->select('id_upload', 'status', 'file_name', 'updated_at')
            ->get()
            ->map(function ($doc) {
                return [
                    'id_upload' => $doc->id_upload,
                    'status' => $doc->status,
                    'file_name' => $doc->file_name,
                    'updated_at' => $doc->updated_at->format('Y-m-d H:i:s'),
                ];
            });
        
        Log::info('Check status multiple documents', [
            'requested_ids' => $ids,
            'found_count' => $documents->count(),
            'user_id' => Auth::id()
        ]);
        
        return response()->json($documents);
    }
}