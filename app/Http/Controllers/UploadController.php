<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\JsonToDatabase; // ⬅️ Ganti ke JsonToDatabase
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    protected $jsonToDatabase;

    public function __construct(JsonToDatabase $jsonToDatabase)
    {
        $this->jsonToDatabase = $jsonToDatabase;
    }
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
            'status' => 'uploaded', // ⬅️ GANTI dari tipe_dokumen
        ]);

        // Kirim file ke Python Flask API
        try {
            $fullPath = storage_path('app/public/' . $path);

            // Update status jadi 'processing'
            $upload->update(['status' => 'processing']);

            Log::info('Sending file to Python', [
                'file' => $originalName,
                'upload_id' => $upload->id_upload
            ]);

            // Kirim ke Python dengan timeout 120 detik (2 menit)
            $response = Http::timeout(120)->attach(
                'file', 
                file_get_contents($fullPath), 
                $originalName
            )->post('http://localhost:5000/process-pdf');

            // Cek apakah response berhasil (status 200-299)
            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('Python API Success', [
                    'upload_id' => $upload->id_upload,
                    'status' => $response->status(),
                    'result' => $result
                ]);

                // ✅ Simpan JSON dari Python
                $upload->update([
                    'status' => 'processing', // masih processing karena belum dipecah
                    'extracted_data' => $result,
                ]);

                // ✅ Proses dan pecah data ke tabel-tabel
                try {
                    $this->jsonToDatabase->process($result, $upload->id_upload);
                    
                    // Update status jadi completed setelah berhasil dipecah
                    $upload->update(['status' => 'completed']);

                    return redirect()->back()->with('success', 'Upload berhasil dan data telah disimpan ke database! ✅');

                } catch (\Exception $e) {
                    Log::error('Failed to process uploaded data', [
                        'upload_id' => $upload->id_upload,
                        'error' => $e->getMessage()
                    ]);

                    $upload->update(['status' => 'failed']);

                    return redirect()->back()->with('error', 'Upload berhasil tapi gagal memproses data: ' . $e->getMessage() . ' ❌');
                }

            } else {
                // Python return error (status 400, 500, dll)
                throw new \Exception('Python API returned error: ' . $response->body());
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Error koneksi ke Python (Python tidak jalan/tidak bisa diakses)
            Log::error('Cannot connect to Python API', [
                'error' => $e->getMessage(),
                'file' => $originalName,
                'upload_id' => $upload->id_upload
            ]);

            $upload->update(['status' => 'failed']);

            return redirect()->back()->with('error', 'Upload berhasil tapi tidak bisa terhubung ke server pemrosesan. Pastikan Python Flask sedang berjalan! ⚠️');

        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Error dari Python API (timeout, dll)
            Log::error('Python API Request Failed', [
                'error' => $e->getMessage(),
                'file' => $originalName,
                'upload_id' => $upload->id_upload
            ]);

            $upload->update(['status' => 'failed']);

            return redirect()->back()->with('error', 'Upload berhasil tapi pemrosesan gagal: ' . $e->getMessage() . ' ❌');

        } catch (\Exception $e) {
            // Error umum lainnya
            Log::error('Failed to process file', [
                'error' => $e->getMessage(),
                'file' => $originalName,
                'upload_id' => $upload->id_upload
            ]);

            $upload->update(['status' => 'failed']);

            return redirect()->back()->with('error', 'Upload berhasil tapi terjadi kesalahan: ' . $e->getMessage() . ' ❌');
        }
    }

    public function storeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'document_type' => 'string|max:50',
        ]);

        $file = $request->file('image');
        $originalName = $file->getClientOriginalName();

        $path = $file->storeAs('images', $originalName, 'public');

        Document::create([
            'source_type' => 'user',
            'id_user' => Auth::id(),
            'source_system' => null,
            'document_type' => $request->document_type ?? 'image',
            'file_name' => $originalName,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'status' => 'uploaded',
        ]);

        return redirect()->back()->with('success', 'Gambar berhasil diupload! ✅');
    }

    public function storeDoc(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:doc,docx|max:10240',
            'document_type' => 'required|string|max:50',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        $path = $file->storeAs('docs', $originalName, 'public');

        Document::create([
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

        return redirect()->back()->with('success', 'Dokumen berhasil diupload! ✅');
    }
}