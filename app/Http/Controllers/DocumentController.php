<?php
// Membaca document yang sudah ada untuk di tampilkan di dasboar
namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::where('id_user', Auth::id())->get();

        return Inertia::render('Documents/Index', [
            'documents' => $documents,
            'activeTab' => 'index', // tanda khusus utk "semua dokumen"
        ]);
    }

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
}
