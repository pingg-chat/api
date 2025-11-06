<?php

declare(strict_types = 1);

use App\Brain\Chat\Tasks\CreateUserTask;
use App\Models\User;
use Illuminate\Validation\ValidationException;

use function Pest\Laravel\assertDatabaseHas;

it('should be able to create a new user', function (): void {
    CreateUserTask::dispatch([
        'icon'     => '',
        'name'     => 'Valid Name',
        'username' => 'validusername',
        'email'    => 'valid@email.com',
        'ssh_key'  => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCy' . sha1('validusername') . ' generated',
    ]);

    assertDatabaseHas('users', [
        'icon'     => '',
        'name'     => 'Valid Name',
        'username' => 'validusername',
        'email'    => 'valid@email.com',
        'ssh_key'  => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCy' . sha1('validusername') . ' generated',
    ]);
});

it('should return the created user', function (): void {
    $task = CreateUserTask::dispatchSync([
        'icon'     => '',
        'name'     => 'Valid Name',
        'username' => 'validusername',
        'email'    => 'valid@email.com',
        'ssh_key'  => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCy' . sha1('validusername') . ' generated',
    ]);

    expect($task->user)->toBeInstanceOf(User::class)
        ->and($task->user->icon)->toBe('')
        ->and($task->user->name)->toBe('Valid Name')
        ->and($task->user->username)->toBe('validusername')
        ->and($task->user->email)->toBe('valid@email.com')
        ->and($task->user->ssh_key)->toBe('ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCy' . sha1('validusername') . ' generated');
});

// ------------------------------------------------------------------------------
// Validations

// --------------------------------------------------
// Icon

test('icon max length is 1', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'icon'     => 'ab',
        'name'     => 'Valid Name',
        'username' => 'validusername',
        'email'    => 'joe@doe.com',
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.max.string', ['attribute' => 'icon', 'max' => 1])
        );
});

test('icon can be null', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'name'     => 'Valid Name',
        'username' => 'validusername',
        'email'    => 'joe@doe.com',
    ]))
        ->not
        ->toThrow(ValidationException::class);
});

// --------------------------------------------------
// Name

test('name should be required', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'name' => null,
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.required', ['attribute' => 'name'])
        );
});

test('name min length is 3', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'name' => 'ab',
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.min.string', ['attribute' => 'name', 'min' => 3])
        );
});

test('name max length is 100', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'name' => str_repeat('a', 101),
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.max.string', ['attribute' => 'name', 'max' => 100])
        );
});

// --------------------------------------------------
// Username
test('username should be required', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'name'     => 'Valid Name',
        'username' => null,
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.required', ['attribute' => 'username'])
        );
});

test('username should be unique', function (): void {
    User::factory()->create([
        'username' => 'existinguser',
    ]);

    expect(fn () => CreateUserTask::dispatchSync([
        'name'     => 'Valid Name',
        'username' => 'existinguser',
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.unique', ['attribute' => 'username'])
        );
});

test('username min length is 3', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'name'     => 'Valid Name',
        'username' => 'ab',
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.min.string', ['attribute' => 'username', 'min' => 3])
        );
});

test('username max length is 100', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'name'     => 'Valid Name',
        'username' => str_repeat('a', 101),
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.max.string', ['attribute' => 'username', 'max' => 100])
        );
});

test('username should be alpha dash', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'name'     => 'Valid Name',
        'username' => 'invalid username!',
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.alpha_dash', ['attribute' => 'username'])
        );
});

// --------------------------------------------------
// Email

test('email should be required', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'name'     => 'Valid Name',
        'username' => 'validusername',
        'email'    => null,
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.required', ['attribute' => 'email'])
        );
});

test('email should be unique', function (): void {
    User::factory()->create([
        'email' => 'joe@doe.com',
    ]);

    expect(fn () => CreateUserTask::dispatchSync([
        'name'     => 'Valid Name',
        'username' => 'validusername',
        'email'    => 'joe@doe.com',
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.unique', ['attribute' => 'email'])
        );
});

test('email should be a valid email', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'name'     => 'Valid Name',
        'username' => 'validusername',
        'email'    => 'not-an-email',
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.email', ['attribute' => 'email'])
        );
});

test('email max length is 100', function (): void {
    expect(fn () => CreateUserTask::dispatchSync([
        'name'     => 'Valid Name',
        'username' => 'validusername',
        'email'    => str_repeat('a', 91) . '@example.com',
    ]))
        ->toThrow(
            ValidationException::class,
            __('validation.max.string', ['attribute' => 'email', 'max' => 100])
        );
});
