<?php

declare(strict_types = 1);

use App\Http\Middleware\CheckKey;
use App\Models\User;

test('check if middlware is register for api routes', function () {
    $route = collect(app('router')->getRoutes()->getRoutesByMethod()['GET'])
        ->first(fn ($route) => $route->uri() === 'me');

    expect($route)->not->toBeNull();
    expect($route->middleware())->toContain(CheckKey::class);
});

test('check if I can pass with the correct key', function () {
    User::factory()->create();

    $this->withHeaders([
        'X-API-KEY'      => config('app.key'),
        'X-Auth-User-Id' => 1,
    ])
        ->get(route('api.me'))
        ->assertStatus(200);
});

test('check if I cannot pass with an incorrect key', function () {
    $this->withHeaders(['X-API-KEY' => 'wrong-key'])
        ->get(route('api.me'))
        ->assertStatus(401);
});
