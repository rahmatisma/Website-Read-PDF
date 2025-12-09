<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    private $ollamaUrl;
    private $model;
    private $timeout;
    
    public function __construct()
    {
        $this->ollamaUrl = env('OLLAMA_URL', 'http://localhost:11434') . '/api/generate';
        $this->model = env('OLLAMA_MODEL', 'phi3:mini');
        $this->timeout = env('OLLAMA_TIMEOUT', 300);
    }
    
    public function sendMessage(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $userMessage = $request->input('message');
        
        // Log user query
        Log::info('Chatbot Query', ['message' => $userMessage]);

        try {
            // Panggil Ollama API
            $response = $this->callOllama($userMessage);
            
            // Log response
            Log::info('Ollama Response', ['response' => $response]);
            
            return response()->json([
                'success' => true,
                'message' => $response,
                'timestamp' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            // Log error
            Log::error('Ollama Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan pada sistem. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function callOllama(string $userMessage): string
    {
        // System prompt untuk konteks chatbot
        $systemPrompt = "Kamu adalah chatbot customer service profesional dari perusahaan teknologi informasi.

INFORMASI PERUSAHAAN:
- Nama: Aplikanusa Lintasarta
- Bidang: Teknologi Informasi & Software Development
- Produk: Software ERP, Mobile Apps, Web Development, Cloud Solutions
- Layanan: Konsultasi IT, Development, Maintenance, Training
- Kontak Email: info@lintasarta.com
- Telepon: (021) 1234-5678
- WhatsApp: 0812-3456-7890
- Alamat: Jakarta Pusat, Indonesia
- Jam Operasional: Senin-Jumat, 09:00-17:00 WIB

INSTRUKSI:
- Jawab dengan ramah dan profesional
- Gunakan bahasa Indonesia
- Maksimal 3-4 kalimat
- Fokus pada informasi perusahaan

Pertanyaan: {$userMessage}

Jawaban:";

        // Kirim request ke Ollama
        $response = Http::timeout($this->timeout)
            ->post($this->ollamaUrl, [
                'model' => $this->model,
                'prompt' => $systemPrompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                    'num_predict' => 200,
                ]
            ]);

        // Cek response
        if (!$response->successful()) {
            throw new \Exception('Ollama API error: ' . $response->status());
        }

        $data = $response->json();
        $botResponse = $data['response'] ?? '';

        if (empty($botResponse)) {
            throw new \Exception('Empty response from Ollama');
        }

        return trim($botResponse);
    }
}