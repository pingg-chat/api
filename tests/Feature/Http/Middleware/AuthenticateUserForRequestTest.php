<?php

declare(strict_types = 1);

use App\Http\Middleware\AuthenticateUserForRequest;
use App\Models\User;

test('check if middlware is register for api routes', function () {
    $route = collect(app('router')->getRoutes()->getRoutesByMethod()['GET'])
        ->first(fn ($route) => $route->uri() === 'me');

    expect($route)->not->toBeNull();
    expect($route->middleware())->toContain(AuthenticateUserForRequest::class);
});

test('make sure that we can authenticate a user for a request', function () {
    $user = User::factory()->create();

    $this->withHeaders([
        'X-API-KEY'      => config('app.key'),
        'X-Auth-User-Id' => $user->id,
    ])->get(route('api.me'))
        ->assertOk()
        ->assertJson([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
        ]);
});
