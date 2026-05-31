<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hardware;

class HardwareController extends Controller
{
    // Dashboard view
    public function index()
    {
        $devices = Hardware::all();

        return view('dashboard', compact('devices'));
    }

    // Update hardware status
    public function update(Request $request, $id)
    {
        $device = Hardware::findOrFail($id);

        $device->update([
            'status' => $request->status,
            'name'   => $request->name ?? $device->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hardware updated!',
            'data'    => $device
        ]);
    }

    // Ambil status device
    public function getStatus($id)
    {
        $device = Hardware::find($id);

        if (!$device) {
            return response()->json([
                'error' => 'Device not found'
            ], 404);
        }

        return response()->json([
            'id'     => $device->id,
            'status' => (int)$device->status
        ]);
    }

    // ESP32 kirim data sensor
    public function sensor(Request $request, $id)
    {
        $request->validate([
            'suhu' => 'required|numeric',
            'kelembapan_udara' => 'required|numeric',
            'kelembapan_tanah' => 'required|numeric',
        ]);

        $device = Hardware::find($id);

        if (!$device) {
            return response()->json([
                'error' => 'Device not found'
            ], 404);
        }

        $device->update([
            'suhu'               => $request->suhu,
            'kelembapan_udara'  => $request->kelembapan_udara,
            'kelembapan_tanah'  => $request->kelembapan_tanah,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sensor updated',
            'data'    => $device
        ]);
    }

    // Ambil semua sensor
    public function allSensor()
    {
        return response()->json(
            Hardware::all()
        );
    }

    // Ambil sensor berdasarkan ID
    public function showSensor($id)
    {
        $device = Hardware::find($id);

        if (!$device) {
            return response()->json([
                'error' => 'Device not found'
            ], 404);
        }

        return response()->json([
            'id' => $device->id,
            'suhu' => $device->suhu,
            'kelembapan_udara' => $device->kelembapan_udara,
            'kelembapan_tanah' => $device->kelembapan_tanah,
        ]);
    }
}
