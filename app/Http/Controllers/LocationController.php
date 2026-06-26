<?php

namespace App\Http\Controllers;

use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Query\ReverseQuery;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Psr\Http\Client\ClientInterface;

class LocationController extends Controller
{
    protected ClientInterface $httpClient;

    public function __construct(?ClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new Client();
    }

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

        $city = null;
        $country = null;

        try {
            // Obtener ciudad y país desde OpenStreetMap vía geocoder-php
            $provider = Nominatim::withOpenStreetMapServer($this->httpClient, 'Nexa App');
            $result = $provider->reverseQuery(ReverseQuery::fromCoordinates($lat, $lng));

            if ($result->count() > 0) {
                $location = $result->first();
                $city = self::cleanCityName($location->getLocality());
                $country = $location->getCountry()?->getName();
            }
        } catch (\Throwable $e) {
            Log::warning('Error al obtener ubicación desde Nominatim', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

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

    public static function cleanCityName(?string $locality): ?string
    {
        if ($locality === null) {
            return null;
        }

        $prefixes = [
            'Perímetro Urbano',
            'Municipio de',
            'Comuna',
            'Zona Urbana',
            'Distrito de',
            'Pueblo de',
            'Villa de',
            'Barrio',
            'Corregimiento',
        ];

        $cleaned = $locality;

        foreach ($prefixes as $prefix) {
            if (str_starts_with($cleaned, $prefix)) {
                $cleaned = trim(substr($cleaned, strlen($prefix)));
                break;
            }
        }

        return $cleaned ?: $locality;
    }
}
