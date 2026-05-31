<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\kaApi; // Pastikan model ini sudah ada

class kaApiController extends Controller
{
    public function chat(Request $request)
    {
        $userMessage = $request->input('message');
        $apiKey = env('AI_API_KEY');

        // Proteksi jika API Key lupa di-set di .env
        if (!$apiKey) {
            return response()->json(['reply' => 'Konfigurasi error: AI_API_KEY tidak ditemukan di .env']);
        }
        $model = env('AI_API_MODEL', 'gemini-2.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . env('AI_API_KEY');
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "Kamu adalah Verdatica Bot, asisten ramah yang ahli dalam tanaman hias di Indonesia. 
           Gunakan gaya bahasa yang santai tapi profesional. 
           Pertanyaan user: " . $userMessage]
                        ]
                    ]
                ]
            ]);

            $data = $response->json();

            if ($response->failed()) {
                return response()->json([
                    'reply' => "Error dari Google: " . ($data['error']['message'] ?? 'Unknown Error')
                ]);
            }

            $botReply = data_get($data, 'candidates.0.content.parts.0.text');

            if (!$botReply) {
                return response()->json([
                    'reply' => "AI merespon tapi teks kosong. Cek sensor keamanan Gemini.",
                    'debug_data' => $data
                ]);
            }

            // SIMPAN KE DATABASE
            // Pastikan kolom ini sesuai dengan migration kamu
            kaApi::create([
                'user_message' => $userMessage,
                'bot_response' => $botReply
            ]);

            return response()->json([
                'reply' => $botReply
            ]);
        } catch (\Exception $e) {
            return response()->json(['reply' => "Error sistem: " . $e->getMessage()]);
        }
    }
}
