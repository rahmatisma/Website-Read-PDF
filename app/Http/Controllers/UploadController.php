<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use Illuminate\Support\Facades\Auth;

class UploadController extends Controller
{
    public function index()
    {
        // ambil semua data upload untuk ditampilkan ke view
        $uploads = Upload::all();

        return inertia('Documents/Index', [
            'uploads' => $uploads
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048',
            'document_type' => 'required|string|max:50',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        // simpan file ke storage/app/public/uploads
        $path = $file->storeAs('uploads', $originalName, 'public');

        // simpan ke database
        Upload::create([
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

        return redirect()->route('documents.index');
    }
}
