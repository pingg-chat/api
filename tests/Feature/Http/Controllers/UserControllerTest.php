<?php

declare(strict_types = 1);

test('GET /api/me returns the authenticated user', function () {
    $user = App\Models\User::factory()->create([
        'name'  => 'John Doe',
        'email' => 'joe@doe.com',
    ]);

    api($user)
        ->get(route('api.me'))
        ->assertStatus(200)
        ->assertJson([
            'id'    => $user->id,
            'name'  => 'John Doe',
            'email' => 'joe@doe.com',
        ]);
});
