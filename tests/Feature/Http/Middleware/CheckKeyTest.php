<?php

declare(strict_types = 1);

use App\Http\Middleware\CheckKey;

test('check if middlware is register for api routes', function () {
    $route = collect(app('router')->getRoutes()->getRoutesByMethod()['GET'])
        ->first(fn ($route) => $route->uri() === 'user');

    expect($route)->not->toBeNull();
    expect($route->middleware())->toContain(CheckKey::class);
});

test('check if I can pass with the correct key', function () {
    $this->withHeaders(['X-API-KEY' => config('app.key')])
        ->get('/user')
        ->assertStatus(200);
});

test('check if I cannot pass with an incorrect key', function () {
    $this->withHeaders(['X-API-KEY' => 'wrong-key'])
        ->get('/user')
        ->assertStatus(401);
});
