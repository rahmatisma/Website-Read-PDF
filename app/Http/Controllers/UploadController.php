<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Models\Upload;
use Illuminate\Support\Facades\Auth;

class UploadController extends Controller
{
    public function index()
    {
        // ambil semua data upload untuk ditampilkan ke view
        $uploads = Document::all();

        return inertia('Documents/Index', [
            'uploads' => $uploads
        ]);
    }

    public function storePDF(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:9048',
            'document_type' => 'required|string|max:50',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        // simpan file ke storage/app/public/uploads
        $path = $file->storeAs('uploads', $originalName, 'public');

        // simpan ke database
        Document::create([
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

        return redirect()->back()->with('success', 'Upload berhasil!');
    }

    public function storeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:9048', 
            // 'image' otomatis validasi mime untuk file gambar
        ]);

        $file = $request->file('image');
        $originalName = $file->getClientOriginalName();

        // simpan file ke storage/app/public/images
        $path = $file->storeAs('images', $originalName, 'public');

        // simpan ke database
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
            'file' => 'required|mimes:doc,docx|max:10048', // max ~10MB
            'document_type' => 'required|string|max:50',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        // simpan file ke storage/app/public/docs
        $path = $file->storeAs('docs', $originalName, 'public');

        // simpan ke database
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




