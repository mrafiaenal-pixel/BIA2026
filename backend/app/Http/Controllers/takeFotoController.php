<?php

namespace App\Http\Controllers;

use App\Models\takeFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class takeFotoController extends Controller
{
    // =========================
    // GET ALL PHOTOS + ANALYSIS
    // =========================
    public function index()
    {
        $photos = takeFoto::latest()->get();

        $photos->transform(function ($photo) {
            $photo->full_url = asset('storage/' . $photo->url);
            return $photo;
        });

        return response()->json($photos);
    }

    // =========================
    // UPLOAD + GEMINI ANALYSIS
    // =========================
    public function analyze(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $file = $request->file('image');

            // 1. simpan gambar
            $path = $file->store('photos', 'public');

            // 2. convert ke base64 untuk Gemini
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            $mimeType = $file->getMimeType();

            // 3. kirim ke Gemini API
            $response = Http::post(
                'https://generativelanguage.googleapis.com/v1beta/models/' . env('AI_API_MODEL', 'gemini-2.5-flash') . ':generateContent?key=' . env('AI_API_KEY'),
                [
                    "contents" => [
                        [
                            "parts" => [
                                [
                                    "text" => "
Kamu adalah ahli tanaman Indonesia.

Analisis gambar tanaman ini dan berikan:
1. Nama tanaman
2. Kondisi tanaman
3. Penyakit (jika ada)
4. Tingkat kesehatan (0-100)
5. Saran perawatan
6. prediksi durasi sampai ke masa panen

Gunakan bahasa Indonesia.
"
                                ],
                                [
                                    "inline_data" => [
                                        "mime_type" => $mimeType,
                                        "data" => $base64
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );

            $result = $response->json();

            // --- KODE PELACAK 1: Jika Google Gemini kirim Error ---
            if (isset($result['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error dari Google Gemini API!',
                    'error_detail' => $result['error']
                ], 400);
            }

            $analysis =
                $result['candidates'][0]['content']['parts'][0]['text']
                ?? 'Analisis tidak tersedia';

            // 4. simpan ke database
            $photoEntry = takeFoto::create([
                'url' => $path,
                'analysis' => $analysis
            ]);

            // 5. response ke frontend
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dianalisis!',
                'data' => [
                    'id' => $photoEntry->id,
                    'full_url' => asset('storage/' . $path),
                    'analysis' => $analysis,
                ]
            ]);
        } catch (\Exception $e) {
            // --- KODE PELACAK 2: Jika sistem Laravel crash ---
            return response()->json([
                'success' => false,
                'message' => 'Gagal analisis sistem: ' . $e->getMessage(),
                'detail_raw' => $result ?? 'Tidak ada response dari Google'
            ], 500);
        }
    }
}
