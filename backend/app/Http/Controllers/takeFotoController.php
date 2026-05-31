<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\takeFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class takeFotoController extends Controller
{
    public function index()
    {
        $photos = \App\Models\takeFoto::latest()->get();

        $photos->transform(function ($photo) {
            $photo->full_url = asset('storage/' . $photo->url);
            return $photo;
        });

        return response()->json($photos);
    }
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $path = $file->store('photos', 'public');


                $photoEntry = takeFoto::create([
                    'url' => $path
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto berhasil disimpan!',
                    'data' => $photoEntry,
                    'full_url' => asset('storage/' . $path)
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload: ' . $e->getMessage()
            ], 500);
        }
    }
}
