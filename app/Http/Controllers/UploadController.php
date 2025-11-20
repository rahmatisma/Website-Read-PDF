<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public function storePDF(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:9048',
            'document_type' => 'required|string|max:50',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        // Simpan file ke storage/app/public/uploads
        $path = $file->storeAs('uploads', $originalName, 'public');

        // Simpan ke database dengan status 'uploaded'
        $document = Document::create([
            'source_type' => 'user',
            'id_user' => Auth::id(),
            'source_system' => null,
            'document_type' => $request->document_type,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'tipe_dokumen' => 'uploaded',
        ]);

        // Kirim file ke Python Flask API
        try {
            // Ambil path lengkap file
            $fullPath = storage_path('app/public/' . $path);

            // Kirim ke Python
            $response = Http::attach(
                'file', 
                file_get_contents($fullPath), 
                $originalName
            )->post('http://localhost:5000/process-pdf');

            // Log response untuk debugging
            Log::info('Python API Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Update status jadi 'processing'
            $document->update(['tipe_dokumen' => 'processing']);

        } catch (\Exception $e) {
            // Jika gagal kirim ke Python, log error
            Log::error('Failed to send file to Python', [
                'error' => $e->getMessage(),
                'file' => $originalName
            ]);

            // Update status jadi 'failed'
            $document->update(['tipe_dokumen' => 'failed']);
        }

        return redirect()->back()->with('success', 'Upload berhasil dan sedang diproses!');
    }

    // Method lain tetap sama (storeImage, storeDoc)
    public function storeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:9048', 
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
            'tipe_dokumen' => 'uploaded',
        ]);

        return redirect()->back()->with('success', 'Upload berhasil!');
    }

    public function storeDoc(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:doc,docx|max:10048',
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
            'tipe_dokumen' => 'uploaded',
        ]);

        return redirect()->back()->with('success', 'Upload berhasil!');
    }
}