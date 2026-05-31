<?php

namespace App\Http\Controllers;

use App\Models\report;
use Illuminate\Http\Request;

class reportController extends Controller
{
    public function index()
    {
        $data = report::latest()->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
    
        $validated = $request->validate([
            'suhu' => 'required',
            'lembap' => 'required',
            'ph' => 'required'
        ]);

        try {
            
            $data = report::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan!',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }
}
