<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $user->forceFill([
            'latitude'  => $validated['lat'],
            'longitude' => $validated['lng'],
        ])->save();

        Log::info('Ubicación actualizada', [
            'user_id' => $user->id,
            'lat'     => $validated['lat'],
            'lng'     => $validated['lng'],
        ]);

        return response()->json([
            'ok'  => true,
            'lat' => (float) $user->latitude,
            'lng' => (float) $user->longitude,
        ]);
    }
}
