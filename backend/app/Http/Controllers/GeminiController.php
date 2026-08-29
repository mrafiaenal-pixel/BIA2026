<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GeminiController extends Controller
{
    public function analyze(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240'
        ]);

        $file = $request->file('image');

        $path = $file->store('photos', 'public');

        $base64 = base64_encode(
            file_get_contents($file->getRealPath())
        );

        $mimeType = $file->getMimeType();

        $response = Http::post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=' . env('GEMINI_API_KEY'),
            [
                "contents" => [
                    [
                        "parts" => [
                            [
                                "text" => "
Kamu adalah ahli tanaman Indonesia.

Analisis tanaman pada gambar ini.

Berikan:

1. Nama tanaman
2. Kondisi tanaman
3. Penyakit jika ada
4. Tingkat kesehatan (0-100)
5. Saran perawatan

Gunakan bahasa Indonesia.
jelaskan secara singkat padat jangan panjang dan point pentingnya saja.
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

        $analysis =
            $result['candidates'][0]['content']['parts'][0]['text']
            ?? 'Analisis gagal';

        $photo = Photo::create([
            'image_path' => $path,
            'analysis' => $analysis
        ]);

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'photo_id' => $photo->id
        ]);
    }

    public function photos()
    {
        $photos = Photo::latest()->get();

        return response()->json(
            $photos->map(function ($photo) {
                return [
                    'id' => $photo->id,
                    'full_url' => asset('storage/' . $photo->image_path),
                    'analysis' => $photo->analysis,
                    'created_at' => $photo->created_at
                ];
            })
        );
    }
}
