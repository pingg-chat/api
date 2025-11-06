<?php

declare(strict_types = 1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'icon'     => $this->faker->randomElement(['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']),
            'name'     => fake()->name(),
            'email'    => fake()->unique()->safeEmail(),
            'username' => fake()->unique()->userName(),
            'ssh_key'  => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCy' . $this->faker->sha1() . ' generated',
        ];
    }
}
