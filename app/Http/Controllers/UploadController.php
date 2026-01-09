<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\JsonToDatabase;
use App\Jobs\ProcessDocumentJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    protected $jsonToDatabase;

    public function __construct(JsonToDatabase $jsonToDatabase)
    {
        $this->jsonToDatabase = $jsonToDatabase;
    }

    /**
     * ========================================
     * VALIDASI DOCUMENT TYPE
     * ========================================
     */
    private function validateDocumentType(string $detectedType, string $expectedCategory): array
    {
        // Definisi kategori dokumen
        $spkTypes = ['spk_survey', 'spk_instalasi', 'spk_dismantle', 'spk_aktivasi'];
        $checklistTypes = ['checklist_wireline', 'checklist_wireless'];
        
        $isValid = false;
        $message = '';
        
        if ($expectedCategory === 'spk') {
            $isValid = in_array($detectedType, $spkTypes);
            if (!$isValid) {
                if (in_array($detectedType, $checklistTypes)) {
                    $message = "Dokumen ini adalah Form Checklist, bukan SPK! Silakan upload di halaman Form Checklist.";
                } elseif ($detectedType === 'unknown') {
                    $message = "Jenis dokumen tidak dapat dideteksi. Pastikan file PDF adalah dokumen SPK yang valid.";
                } else {
                    $message = "Jenis dokumen tidak sesuai untuk upload SPK.";
                }
            }
        } elseif ($expectedCategory === 'checklist') {
            $isValid = in_array($detectedType, $checklistTypes);
            if (!$isValid) {
                if (in_array($detectedType, $spkTypes)) {
                    $message = "Dokumen ini adalah SPK, bukan Form Checklist! Silakan upload di halaman Dokumen PDF.";
                } elseif ($detectedType === 'unknown') {
                    $message = "Jenis dokumen tidak dapat dideteksi. Pastikan file PDF adalah Form Checklist yang valid.";
                } else {
                    $message = "Jenis dokumen tidak sesuai untuk upload Form Checklist.";
                }
            }
        }
        
        return [
            'valid' => $isValid,
            'message' => $message,
            'detected_type' => $detectedType
        ];
    }

    /**
     * ========================================
     * UPLOAD SPK - DENGAN BACKGROUND JOB + VALIDASI
     * ========================================
     */
    public function storespk(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240', // 10MB
            'document_type' => 'required|string|max:50',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        // Simpan file ke storage/app/public/uploads
        $path = $file->storeAs('uploads', $originalName, 'public');

        // Simpan ke database dengan status 'uploaded'
        $upload = Document::create([
            'source_type' => 'user',
            'id_user' => Auth::id(),
            'source_system' => null,
            'document_type' => $request->document_type,
            'file_name' => $originalName,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'status' => 'uploaded',
        ]);

        Log::info('PDF uploaded successfully, dispatching background job', [
            'upload_id' => $upload->id_upload,
            'file_name' => $originalName,
            'document_type' => $request->document_type,
            'file_size' => $file->getSize(),
            'expected_category' => 'spk' // ← TAMBAHAN: untuk validasi di Job
        ]);

        // ✅ DISPATCH JOB KE QUEUE dengan parameter validasi
        ProcessDocumentJob::dispatch($upload->id_upload, 'spk');

        return redirect()->back()->with('success', 'Upload berhasil! Dokumen SPK sedang diproses di background. Refresh halaman untuk melihat status. 🚀');
    }

    /**
     * ========================================
     * UPLOAD FORM CHECKLIST - DENGAN BACKGROUND JOB + VALIDASI
     * ========================================
     */
    public function storeChecklist(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240', // 10MB
            'document_type' => 'required|string|max:50',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        // Simpan file ke storage/app/public/checklists
        $path = $file->storeAs('checklists', $originalName, 'public');

        // Simpan ke database dengan status 'uploaded'
        $upload = Document::create([
            'source_type' => 'user',
            'id_user' => Auth::id(),
            'source_system' => null,
            'document_type' => 'form_checklist',
            'file_name' => $originalName,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'status' => 'uploaded',
        ]);

        Log::info('Form Checklist PDF uploaded successfully, dispatching background job', [
            'upload_id' => $upload->id_upload,
            'file_name' => $originalName,
            'document_type' => 'form_checklist',
            'file_size' => $file->getSize(),
            'expected_category' => 'checklist' // ← TAMBAHAN: untuk validasi di Job
        ]);

        // ✅ DISPATCH JOB KE QUEUE dengan parameter validasi
        ProcessDocumentJob::dispatch($upload->id_upload, 'checklist');

        return redirect()->back()->with('success', 'Upload form checklist berhasil! Dokumen sedang diproses di background. Refresh halaman untuk melihat status. 🚀');
    }


    /**
     * ========================================
     * LIHAT STATUS DOKUMEN
     * ========================================
     */
    public function status()
    {
        $documents = Document::where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('documents.status', compact('documents'));
    }

    /**
     * ========================================
     * GET STATUS DOCUMENT (AJAX)
     * ========================================
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
            'status' => $document->status,
            'file_name' => $document->file_name,
            'created_at' => $document->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $document->updated_at->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * ========================================
     * RETRY PROCESSING (Manual)
     * ========================================
     */
    public function retry($id)
    {
        $document = Document::where('id_upload', $id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$document) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan! ❌');
        }

        if ($document->status !== 'failed') {
            return redirect()->back()->with('error', 'Dokumen ini tidak perlu di-retry (status: ' . $document->status . ')');
        }

        // Update status ke uploaded dan dispatch ulang
        $document->update(['status' => 'uploaded']);

        Log::info('Retry processing document', [
            'upload_id' => $document->id_upload,
            'file_name' => $document->file_name
        ]);

        // Tentukan kategori berdasarkan path atau document_type
        $category = 'spk'; // default
        if (str_contains($document->file_path, 'checklists/') || 
            $document->document_type === 'form_checklist') {
            $category = 'checklist';
        }

        ProcessDocumentJob::dispatch($document->id_upload, $category);

        return redirect()->back()->with('success', 'Dokumen akan diproses ulang di background! 🔄');
    }
}