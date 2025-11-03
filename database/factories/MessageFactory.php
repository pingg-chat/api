<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'channel_id' => Channel::factory(),
            'user_id'    => User::factory(),
            'content'    => $this->faker->sentence(),
        ];
    }
}
