<?php

declare(strict_types = 1);

it('should return a collection of workspaces for the authenticated user', function () {
    $user       = App\Models\User::factory()->create();
    $workspaces = App\Models\Workspace::factory()->count(3)->create();
    $user->workspaces()->attach($workspaces->pluck('id')->toArray());

    $response = api($user)
        ->get(route('api.workspaces'))
        ->assertStatus(200)
        ->assertJsonStructure([
            '*' => [
                'id',
                'icon',
                'name',
                'description',
            ],
        ]);

    $responseData = $response->json();
    expect(count($responseData))->toBe(3);
});
