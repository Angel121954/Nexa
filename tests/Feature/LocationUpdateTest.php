<?php

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psr\Http\Client\ClientInterface;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

function mockGuzzleClient(array $responses): Client
{
    $mock = new MockHandler($responses);
    $handlerStack = HandlerStack::create($mock);
    return new Client(['handler' => $handlerStack]);
}

function nominatimResponse(string $city, string $country, float $lat, float $lng): Response
{
    return new Response(200, [], json_encode([
        'place_id'     => 123,
        'licence'      => 'test',
        'osm_type'     => 'node',
        'osm_id'       => 456,
        'lat'          => (string) $lat,
        'lon'          => (string) $lng,
        'display_name' => "$city, $country",
        'address'      => [
            'city'    => $city,
            'country' => $country,
        ],
        'boundingbox' => [(string) ($lat - 0.01), (string) ($lat + 0.01), (string) ($lng - 0.01), (string) ($lng + 0.01)],
    ]));
}

it('actualiza current_city y current_country via geocoder-php', function () {
    $client = mockGuzzleClient([
        nominatimResponse('Madrid', 'Spain', 40.4168, -3.7038),
    ]);

    $this->app->instance(ClientInterface::class, $client);

    $this->actingAs($this->user);

    $response = $this->postJson('/api/update-location', [
        'lat' => 40.4168,
        'lng' => -3.7038,
    ]);

    $response->assertOk();
    $response->assertJson(['ok' => true]);

    $this->user->refresh();

    expect($this->user->current_latitude)->toBe((float) 40.4168);
    expect($this->user->current_longitude)->toBe((float) -3.7038);
    expect($this->user->current_city)->toBe('Madrid');
    expect($this->user->current_country)->toBe('Spain');
});

it('guarda home_location solo la primera vez', function () {
    $mock = new MockHandler([
        nominatimResponse('Madrid', 'Spain', 40.4168, -3.7038),
        nominatimResponse('Barcelona', 'Spain', 41.3874, 2.1686),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);
    $this->app->instance(ClientInterface::class, $client);

    $this->actingAs($this->user);

    $this->postJson('/api/update-location', ['lat' => 40.4168, 'lng' => -3.7038]);
    $this->user->refresh();
    expect($this->user->home_latitude)->toBe((float) 40.4168);
    expect($this->user->home_city)->toBe('Madrid');

    $response2 = $this->postJson('/api/update-location', ['lat' => 41.3874, 'lng' => 2.1686]);
    $this->user->refresh();

    expect($this->user->current_city)->toBe('Barcelona');
    expect($this->user->home_city)->toBe('Madrid');
    $response2->assertJson(['traveling' => true]);
});

it('limpia prefijos administrativos del nombre de la ciudad', function () {
    $client = mockGuzzleClient([
        nominatimResponse('Perímetro Urbano Medellín', 'Colombia', 6.2476, -75.5658),
    ]);

    $this->app->instance(ClientInterface::class, $client);

    $this->actingAs($this->user);

    $this->postJson('/api/update-location', ['lat' => 6.2476, 'lng' => -75.5658]);
    $this->user->refresh();

    expect($this->user->current_city)->toBe('Medellín');
    expect($this->user->current_country)->toBe('Colombia');
});

it('limpia Municipio de prefijo', function () {
    $client = mockGuzzleClient([
        nominatimResponse('Municipio de Querétaro', 'México', 20.5888, -100.3899),
    ]);

    $this->app->instance(ClientInterface::class, $client);

    $this->actingAs($this->user);

    $this->postJson('/api/update-location', ['lat' => 20.5888, 'lng' => -100.3899]);
    $this->user->refresh();

    expect($this->user->current_city)->toBe('Querétaro');
});

it('rechaza coordenadas inválidas', function () {
    $this->actingAs($this->user);

    $response = $this->postJson('/api/update-location', [
        'lat' => 100,
        'lng' => 200,
    ]);

    $response->assertStatus(422);
});
