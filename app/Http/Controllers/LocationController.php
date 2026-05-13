<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $user->latitude = $request->lat;
        $user->longitude = $request->lng;
        $user->save();

        return response()->json([
            'ok' => true,
            'lat' => $user->latitude,
            'lng' => $user->longitude
        ]);
    }
}
