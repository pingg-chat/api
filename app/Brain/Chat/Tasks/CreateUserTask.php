<?php

declare(strict_types = 1);

namespace App\Brain\Chat\Tasks;

use App\Models\User;
use Brain\Task;

/**
 * Task CreateUserTask
 *
 * @property-read string $icon
 * @property-read string $email
 * @property-read string $name
 * @property-read string $username
 * @property-read string $ssh_key
 *
 * @property User $user
 */
class CreateUserTask extends Task
{
    public function rules(): array
    {
        return [
            'icon'     => ['nullable', 'string', 'max:1'], // 
            'name'     => ['required', 'min:3', 'max:100'],
            'username' => ['required', 'min:3', 'max:100', 'unique:users,username', 'alpha_dash'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'ssh_key'  => ['required', 'string', 'unique:users,ssh_key'],
        ];
    }

    public function handle(): self
    {
        $this->user = User::query()
            ->create([
                'icon'     => $this->icon ?: '',
                'name'     => $this->name,
                'username' => $this->username,
                'email'    => $this->email,
                'ssh_key'  => $this->ssh_key,
            ]);

        return $this;
    }
}
