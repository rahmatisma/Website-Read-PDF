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
     * UPLOAD PDF - DENGAN BACKGROUND JOB
     * ========================================
     * User langsung redirect tanpa tunggu processing selesai.
     * Semua proses berat (Python API, ekstraksi, parsing) dilakukan di background.
     */
    public function storePDF(Request $request)
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
            'file_size' => $file->getSize()
        ]);

        // ✅ DISPATCH JOB KE QUEUE
        // Proses yang akan dilakukan di background:
        // 1. Update status → 'processing'
        // 2. Kirim file ke Python API (timeout 10 menit)
        // 3. Simpan extracted_data (JSON dari Python)
        // 4. Pecah data ke tabel-tabel (JsonToDatabase->process)
        // 5. Update status → 'completed' / 'failed'
        ProcessDocumentJob::dispatch($upload->id_upload);

        // ✅ LANGSUNG RETURN - User tidak perlu tunggu
        return redirect()->back()->with('success', 'Upload berhasil! Dokumen sedang diproses di background. Refresh halaman untuk melihat status. 🚀');
    }

    /**
     * ========================================
     * UPLOAD IMAGE - SYNCHRONOUS (Cepat)
     * ========================================
     * Tidak perlu background job karena upload gambar cepat
     */
    public function storeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240', // 10MB
            'document_type' => 'string|max:50',
        ]);

        try {
            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();

            // Simpan file ke storage/app/public/images
            $path = $file->storeAs('images', $originalName, 'public');

            // Simpan ke database
            $upload = Document::create([
                'source_type' => 'user',
                'id_user' => Auth::id(),
                'source_system' => null,
                'document_type' => $request->document_type ?? 'image',
                'file_name' => $originalName,
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'status' => 'completed', // Langsung completed karena tidak perlu processing
            ]);

            Log::info('Image uploaded successfully', [
                'upload_id' => $upload->id_upload,
                'file_name' => $originalName,
                'file_size' => $file->getSize()
            ]);

            return redirect()->back()->with('success', 'Gambar berhasil diupload! ✅');

        } catch (\Exception $e) {
            Log::error('Failed to upload image', [
                'error' => $e->getMessage(),
                'file_name' => $request->file('image')->getClientOriginalName()
            ]);

            return redirect()->back()->with('error', 'Upload gambar gagal: ' . $e->getMessage() . ' ❌');
        }
    }

    /**
     * ========================================
     * UPLOAD DOCUMENT (DOC/DOCX) - SYNCHRONOUS
     * ========================================
     * Tidak perlu background job karena hanya upload file
     */
    public function storeDoc(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:doc,docx|max:10240', // 10MB
            'document_type' => 'required|string|max:50',
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();

            // Simpan file ke storage/app/public/docs
            $path = $file->storeAs('docs', $originalName, 'public');

            // Simpan ke database
            $upload = Document::create([
                'source_type' => 'user',
                'id_user' => Auth::id(),
                'source_system' => null,
                'document_type' => $request->document_type,
                'file_name' => $originalName,
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'status' => 'completed', // Langsung completed karena tidak perlu processing
            ]);

            Log::info('Document uploaded successfully', [
                'upload_id' => $upload->id_upload,
                'file_name' => $originalName,
                'file_size' => $file->getSize()
            ]);

            return redirect()->back()->with('success', 'Dokumen berhasil diupload! ✅');

        } catch (\Exception $e) {
            Log::error('Failed to upload document', [
                'error' => $e->getMessage(),
                'file_name' => $request->file('file')->getClientOriginalName()
            ]);

            return redirect()->back()->with('error', 'Upload dokumen gagal: ' . $e->getMessage() . ' ❌');
        }
    }

     /**
     * ========================================
     * UPLOAD FORM CHECKLIST - DENGAN BACKGROUND JOB
     * ========================================
     * Sama seperti upload PDF, form checklist juga menggunakan background job.
     * Semua proses berat (Python API, ekstraksi, parsing) dilakukan di background.
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
             'file_size' => $file->getSize()
         ]);
 
         // ✅ DISPATCH JOB KE QUEUE (sama seperti PDF)
         ProcessDocumentJob::dispatch($upload->id_upload);
 
         // ✅ LANGSUNG RETURN - User tidak perlu tunggu
         return redirect()->back()->with('success', 'Upload form checklist berhasil! Dokumen sedang diproses di background. Refresh halaman untuk melihat status. 🚀');
     }

    /**
     * ========================================
     * LIHAT STATUS DOKUMEN
     * ========================================
     * Menampilkan semua dokumen user dengan status processing/completed/failed
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
     * Untuk cek status dokumen via AJAX (polling)
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
     * Jika dokumen failed, user bisa retry manual
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

        ProcessDocumentJob::dispatch($document->id_upload);

        return redirect()->back()->with('success', 'Dokumen akan diproses ulang di background! 🔄');
    }
}