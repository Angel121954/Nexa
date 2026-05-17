<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'No autenticado'
            ], 401);
        }

        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $lat = $validated['lat'];
        $lng = $validated['lng'];

        // Obtener ciudad y país desde OpenStreetMap
        $response = Http::withHeaders([
            'User-Agent' => 'Nexa App'
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'format' => 'json',
            'lat'    => $lat,
            'lon'    => $lng,
        ]);

        $data = $response->json();

        $city =
            $data['address']['city']
            ?? $data['address']['town']
            ?? $data['address']['village']
            ?? $data['address']['state']
            ?? null;

        $country =
            $data['address']['country']
            ?? null;

        // Guardar ubicación actual
        $user->forceFill([

            'current_latitude'  => $lat,
            'current_longitude' => $lng,
            'current_city'      => $city,
            'current_country'   => $country,

        ])->save();

        // Guardar ubicación principal SOLO una vez
        if (!$user->home_latitude) {

            $user->forceFill([

                'home_latitude'  => $lat,
                'home_longitude' => $lng,
                'home_city'      => $city,
                'home_country'   => $country,

            ])->save();
        }

        Log::info('Ubicación actualizada', [
            'user_id' => $user->id,
            'city'    => $city,
            'country' => $country,
        ]);

        return response()->json([

            'ok' => true,

            'home_city'    => $user->home_city,
            'current_city' => $user->current_city,

            'traveling' =>
            $user->home_city !== $user->current_city
        ]);
    }
}
