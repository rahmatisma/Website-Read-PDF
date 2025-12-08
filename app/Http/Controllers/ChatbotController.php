<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $userMessage = $request->input('message');
        
        // Logic untuk filtering query berdasarkan ketentuan perusahaan
        $response = $this->processMessage($userMessage);

        return response()->json([
            'success' => true,
            'message' => $response,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    private function processMessage(string $message)
    {
        // Daftar keyword yang berkaitan dengan perusahaan
        $allowedKeywords = ['perusahaan', 'company', 'produk', 'layanan', 'service', 'kontak', 'alamat', 'tentang'];
        
        $messageWords = strtolower($message);
        $isValid = false;

        foreach ($allowedKeywords as $keyword) {
            if (str_contains($messageWords, $keyword)) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            return 'Maaf, saya hanya dapat membantu pertanyaan yang berkaitan dengan perusahaan kami. Silakan tanyakan tentang produk, layanan, atau informasi perusahaan.';
        }

        // Di sini bisa diintegrasikan dengan AI/API lain atau database
        // Contoh response sederhana:
        return $this->generateResponse($messageWords);
    }

    private function generateResponse(string $message)
    {
        // Contoh response berdasarkan keyword
        if (str_contains($message, 'produk')) {
            return 'Kami menyediakan berbagai produk berkualitas tinggi. Untuk informasi lebih detail, silakan hubungi tim sales kami.';
        }
        
        if (str_contains($message, 'layanan') || str_contains($message, 'service')) {
            return 'Kami menawarkan layanan profesional untuk memenuhi kebutuhan bisnis Anda. Layanan kami meliputi konsultasi, implementasi, dan support.';
        }
        
        if (str_contains($message, 'kontak')) {
            return 'Anda dapat menghubungi kami melalui email: info@perusahaan.com atau telepon: (021) 1234-5678';
        }
        
        if (str_contains($message, 'alamat')) {
            return 'Kantor kami berlokasi di Jakarta, Indonesia. Untuk alamat lengkap, silakan kunjungi halaman kontak kami.';
        }
        
        if (str_contains($message, 'tentang') || str_contains($message, 'perusahaan') || str_contains($message, 'company')) {
            return 'Kami adalah perusahaan yang bergerak di bidang teknologi informasi, menyediakan solusi digital untuk berbagai industri.';
        }

        return 'Terima kasih atas pertanyaan Anda. Tim kami akan segera membantu Anda. Silakan hubungi customer service untuk informasi lebih lanjut.';
    }
}