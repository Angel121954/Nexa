<?php

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    // SQLite no tiene acos/cos/sin/radians — los registramos para el test
    $pdo = DB::connection()->getPdo();
    $pdo->sqliteCreateFunction('acos', 'acos');
    $pdo->sqliteCreateFunction('cos', 'cos');
    $pdo->sqliteCreateFunction('sin', 'sin');
    $pdo->sqliteCreateFunction('radians', fn($x) => deg2rad($x));

    $this->me = User::factory()->create([
        'current_latitude'  => 40.4168,
        'current_longitude' => -3.7038,
    ]);

    $this->me->profile()->create(['profile_completed' => true]);

    // Usuario muy cerca (~1.2km)
    $near = User::factory()->create([
        'current_latitude'  => 40.4200,
        'current_longitude' => -3.6900,
    ]);
    $near->profile()->create(['profile_completed' => true]);

    // Usuario lejos (~1600km, Berlín)
    $far = User::factory()->create([
        'current_latitude'  => 52.5200,
        'current_longitude' => 13.4050,
    ]);
    $far->profile()->create(['profile_completed' => true]);
});

it('filtra usuarios cercanos usando current_latitude/current_longitude', function () {
    $this->actingAs($this->me);

    $response = $this->getJson('/explore?nearby=1&distance=10');

    $response->assertOk();
    $data = $response->json();

    expect($data['count'])->toBe(1);
});

it('excluye usuarios fuera del radio', function () {
    $this->actingAs($this->me);

    $response = $this->getJson('/explore?nearby=1&distance=1');

    $response->assertOk();
    $data = $response->json();

    expect($data['count'])->toBe(0);
});

it('no filtra por nearby si el usuario no tiene coordenadas', function () {
    $userWithoutLocation = User::factory()->create();
    $userWithoutLocation->profile()->create(['profile_completed' => true]);

    $this->actingAs($userWithoutLocation);

    $response = $this->getJson('/explore?nearby=1');

    $response->assertOk();
    $data = $response->json();

    expect($data['count'])->toBeGreaterThanOrEqual(2);
});

it('ordena por distancia ascendente', function () {
    $this->actingAs($this->me);

    $response = $this->getJson('/explore?nearby=1&distance=50000');
    $response->assertOk();

    $data = $response->json();
    expect($data['count'])->toBe(2);
});
