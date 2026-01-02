<?php
// Membaca document yang sudah ada untuk di tampilkan di dashboard
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\SPK;
use App\Models\User;
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
                // ✅ FIXED: PDF yang BUKAN form checklist (cek lewat relasi SPK)
                $query->where('file_type', 'pdf')
                    ->where('document_type', '!=', 'form_checklist');
                break;
            case 'doc':
                $query->whereIn('file_type', ['doc', 'docx']);
                break;
            case 'gambar':
                $query->whereIn('file_type', ['jpg', 'jpeg', 'png']);
                break;
            case 'form-checklist':
                // ✅ FIXED: Ambil dokumen yang punya SPK dengan type form_checklist
                $query->where('document_type', 'form_checklist');
                break;
            default:
                abort(404);
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

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
    
    public function dashboard()
    {
        $userId = Auth::id();
        $currentUser = Auth::user();

        // ========================================
        // AUTO DELETE: Hapus dokumen > 1 hari
        // ========================================
        $oneDayAgo = now()->subDay();
        
        $oldDocuments = Document::where('created_at', '<', $oneDayAgo)->get();
        
        foreach ($oldDocuments as $doc) {
            if (Storage::exists('public/' . $doc->file_path)) {
                Storage::delete('public/' . $doc->file_path);
            }
            $doc->delete();
        }

        Log::info('Auto-delete dokumen lama', [
            'jumlah_dihapus' => $oldDocuments->count(),
            'cutoff_date' => $oneDayAgo
        ]);

        // ========================================
        // STATISTIK DOKUMEN USER
        // ========================================
        // PDF yang BUKAN form checklist
        $countPDF = Document::where('id_user', $userId)
            ->where('file_type', 'pdf')
            ->whereDoesntHave('spks', function($q) {
                $q->whereIn('document_type', ['form_checklist_wireline', 'form_checklist_wireless']);
            })
            ->count();

        $countDOC = Document::where('id_user', $userId)
            ->whereIn('file_type', ['doc', 'docx'])
            ->count();

        $countIMG = Document::where('id_user', $userId)
            ->whereIn('file_type', ['jpg', 'jpeg', 'png'])
            ->count();
        
        // ✅ FIXED: Form Checklist dari SPK yang punya id_upload milik user ini
        $countChecklist = Document::where('id_user', $userId)
            ->whereHas('spks', function($q) {
                $q->whereIn('document_type', ['form_checklist_wireline', 'form_checklist_wireless']);
            })
            ->count();

        $countAll = Document::where('id_user', $userId)->count();

        // ========================================
        // STATISTIK USERS (khusus untuk admin)
        // ========================================
        $countUsersUnverified = 0;
        $unverifiedUsers = collect([]);
        
        if ($currentUser && $currentUser->isAdmin()) {
            $countUsersUnverified = User::where('is_verified_by_admin', false)->count();
            
            $unverifiedUsers = User::where('is_verified_by_admin', false)
                ->select(['id', 'name', 'email', 'role', 'avatar', 'email_verified_at', 'created_at', 'updated_at'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // ========================================
        // ✅ FIXED: STATISTIK SPK (Global - semua user)
        // ========================================
        // Total jenis SPK yang unik
        $countSPKTypes = SPK::where('document_type', 'spk')
            ->distinct('jenis_spk')
            ->count('jenis_spk');

        // ✅ FIXED: Total form checklist (wireline + wireless)
        $countFormChecklist = SPK::whereIn('document_type', [
            'form_checklist_wireline', 
            'form_checklist_wireless'
        ])->count();

        // ========================================
        // ✅ FIXED: UPLOAD TREND (7 hari terakhir)
        // ========================================
        $uploadTrend = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('d M');
            
            // SPK: Upload yang punya SPK dengan document_type = 'spk'
            $spkCount = Document::whereDate('created_at', $date->toDateString())
                ->whereHas('spks', function($q) {
                    $q->where('document_type', 'spk');
                })
                ->count();
            
            // Checklist: Upload yang punya SPK dengan document_type = form_checklist_*
            $checklistCount = Document::whereDate('created_at', $date->toDateString())
                ->whereHas('spks', function($q) {
                    $q->whereIn('document_type', ['form_checklist_wireline', 'form_checklist_wireless']);
                })
                ->count();
            
            $uploadTrend[] = [
                'date' => $dateStr,
                'spk' => $spkCount,
                'checklist' => $checklistCount,
            ];
        }

        // ========================================
        // RECENT DOCUMENTS (5 terbaru)
        // ========================================
        $recentDocuments = Document::where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id_upload,
                    'fileName' => $doc->file_name,
                    'uploadedDate' => $doc->created_at->format('Y-m-d H:i:s'),
                    'fileSize' => $doc->file_size ? number_format($doc->file_size / 1024 / 1024, 2) . ' MB' : '-',
                    'status' => $doc->status ?? 'completed',
                ];
            });

        return Inertia::render('dashboard', [
            'countPDF'  => $countPDF,
            'countDOC'  => $countDOC,
            'countIMG'  => $countIMG,
            'countChecklist' => $countChecklist,
            'countAll'  => $countAll,
            'countUsersUnverified' => $countUsersUnverified,
            'countSPKTypes' => $countSPKTypes,
            'countFormChecklist' => $countFormChecklist,
            'uploadTrend' => $uploadTrend,
            'recentDocuments' => $recentDocuments,
            'unverifiedUsers' => $unverifiedUsers,
        ]);
    }
    
    /**
     * ✅ FIXED: GET DASHBOARD STATS (untuk real-time updates)
     */
    public function getDashboardStats()
    {
        $userId = Auth::id();
        $currentUser = Auth::user();

        // ========================================
        // STATISTIK USERS (khusus untuk admin)
        // ========================================
        $countUsersUnverified = 0;
        $unverifiedUsers = collect([]);
        
        if ($currentUser && $currentUser->isAdmin()) {
            $countUsersUnverified = User::where('is_verified_by_admin', false)->count();
            
            $unverifiedUsers = User::where('is_verified_by_admin', false)
                ->select(['id', 'name', 'email', 'role', 'avatar', 'email_verified_at', 'created_at', 'updated_at'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // ========================================
        // ✅ FIXED: STATISTIK SPK
        // ========================================
        $countSPKTypes = SPK::where('document_type', 'spk')
            ->distinct('jenis_spk')
            ->count('jenis_spk');

        $countFormChecklist = SPK::whereIn('document_type', [
            'form_checklist_wireline', 
            'form_checklist_wireless'
        ])->count();

        // ========================================
        // ✅ FIXED: UPLOAD TREND
        // ========================================
        $uploadTrend = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('d M');
            
            $spkCount = Document::whereDate('created_at', $date->toDateString())
                ->whereHas('spks', function($q) {
                    $q->where('document_type', 'spk');
                })
                ->count();
            
            $checklistCount = Document::whereDate('created_at', $date->toDateString())
                ->whereHas('spks', function($q) {
                    $q->whereIn('document_type', ['form_checklist_wireline', 'form_checklist_wireless']);
                })
                ->count();
            
            $uploadTrend[] = [
                'date' => $dateStr,
                'spk' => $spkCount,
                'checklist' => $checklistCount,
            ];
        }

        // ========================================
        // RECENT DOCUMENTS
        // ========================================
        $recentDocuments = Document::where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id_upload,
                    'fileName' => $doc->file_name,
                    'uploadedDate' => $doc->created_at->format('Y-m-d H:i:s'),
                    'fileSize' => $doc->file_size ? number_format($doc->file_size / 1024 / 1024, 2) . ' MB' : '-',
                    'status' => $doc->status ?? 'completed',
                ];
            });

        return response()->json([
            'countUsersUnverified' => $countUsersUnverified,
            'countSPKTypes' => $countSPKTypes,
            'countFormChecklist' => $countFormChecklist,
            'uploadTrend' => $uploadTrend,
            'recentDocuments' => $recentDocuments,
            'unverifiedUsers' => $unverifiedUsers,
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
            
            // ========================================
            // 1. HAPUS SPK DAN CEK JARINGAN
            // ========================================
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
            
            // ========================================
            // 2. HAPUS FILE PDF ASLI
            // ========================================
            if (Storage::exists('public/' . $upload->file_path)) {
                Storage::delete('public/' . $upload->file_path);
                Log::info('File PDF asli berhasil dihapus dari storage', [
                    'path' => $upload->file_path
                ]);
            } else {
                Log::warning('File PDF asli tidak ditemukan di storage', [
                    'path' => $upload->file_path
                ]);
            }
            
            // ========================================
            // 3. HAPUS FOLDER HASIL EKSTRAKSI PYTHON
            // ========================================
            if ($upload->extracted_data && isset($upload->extracted_data['dokumentasi']) && is_array($upload->extracted_data['dokumentasi'])) {
                $deletedFiles = 0;
                $extractedFolder = null;
                
                foreach ($upload->extracted_data['dokumentasi'] as $doc) {
                    if (isset($doc['patch_foto'])) {
                        // Path dari database: output/extracted/spk/survey/survey_xxx/images/foto.jpg
                        $relativePath = $doc['patch_foto'];
                        
                        // Full path: storage/app/public/output/extracted/...
                        $fullPath = storage_path('app/public/' . $relativePath);
                        
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                            $deletedFiles++;
                            
                            Log::info('Gambar dokumentasi berhasil dihapus', [
                                'relative_path' => $relativePath,
                                'full_path' => $fullPath
                            ]);
                        } else {
                            Log::warning('Gambar dokumentasi tidak ditemukan', [
                                'relative_path' => $relativePath,
                                'full_path' => $fullPath
                            ]);
                        }
                        
                        // Extract folder induk (survey_xxx)
                        if (!$extractedFolder) {
                            // output/extracted/spk/survey/survey_20241230_143022/images/foto.jpg
                            // → output/extracted/spk/survey/survey_20241230_143022
                            $pathParts = explode('/', $relativePath);
                            if (count($pathParts) >= 5) {
                                $extractedFolder = implode('/', array_slice($pathParts, 0, -2));
                            }
                        }
                    }
                }
                
                Log::info('Ringkasan penghapusan gambar dokumentasi', [
                    'total_gambar_dihapus' => $deletedFiles,
                    'extracted_folder' => $extractedFolder
                ]);
                
                // ========================================
                // 4. HAPUS FOLDER INDUK (survey_xxx, wireline_xxx, dll)
                // ========================================
                if ($extractedFolder) {
                    $extractedFolderPath = storage_path('app/public/' . $extractedFolder);
                    
                    if (is_dir($extractedFolderPath)) {
                        // Hapus semua isi folder (images, JSON, dll)
                        $this->deleteDirectory($extractedFolderPath);
                        
                        Log::info('Folder extracted berhasil dihapus', [
                            'folder_path' => $extractedFolder,
                            'full_path' => $extractedFolderPath
                        ]);
                    } else {
                        Log::warning('Folder extracted tidak ditemukan', [
                            'folder_path' => $extractedFolder,
                            'full_path' => $extractedFolderPath
                        ]);
                    }
                }
            } else {
                Log::info('Tidak ada data dokumentasi untuk dihapus');
            }
            
            // ========================================
            // 5. HAPUS RECORD DATABASE
            // ========================================
            $upload->delete();
            
            // ========================================
            // 6. HAPUS JARINGAN JIKA TIDAK ADA SPK LAGI
            // ========================================
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
            
            Log::info('✅ SEMUA DATA BERHASIL DIHAPUS', [
                'id_upload' => $id,
                'jumlah_spk' => count($spks),
                'jumlah_jaringan_dicek' => count($noJaringanList),
                'success' => true
            ]);
            
            return redirect()->back()->with('success', 'Dokumen, file PDF, folder ekstraksi, dan semua data terkait berhasil dihapus! ✅');
            
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
     * Helper: Hapus direktori beserta isinya secara rekursif
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }

    /**
     * ✅ DETAIL DOKUMEN - FIXED: Wrap extracted_data dengan "data"
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
            
            // ✅ FIXED: Transform extracted_data sesuai struktur yang diharapkan frontend
            $extractedData = null;
            if ($upload->extracted_data) {
                $rawData = is_string($upload->extracted_data) 
                    ? json_decode($upload->extracted_data, true) 
                    : $upload->extracted_data;
                
                // ✅ Wrap dengan "data" agar sesuai dengan interface TypeScript
                $extractedData = [
                    'data' => $rawData
                ];
            }

            // ✅ Check via relasi SPK
            $isChecklist = $upload->spks()
                ->whereIn('document_type', ['form_checklist_wireline', 'form_checklist_wireless'])
                ->exists();
            
            $component = $isChecklist ? 'Documents/FormChecklistDetail' : 'Documents/Detail';
            
            return Inertia::render($component, [
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
                'extractedData' => $extractedData, // ✅ Sudah wrapped dengan "data"
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
     * ✅ GET STATUS SINGLE DOCUMENT
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
     * ✅ CHECK STATUS MULTIPLE DOCUMENTS
     */
    public function checkStatus(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'error' => 'IDs harus berupa array dan tidak boleh kosong'
            ], 400);
        }
        
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