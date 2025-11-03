<?php

declare(strict_types = 1);

namespace App\Brain\Chat\Tasks;

use App\Events\MessageWasCreated;
use App\Models\Message;
use Brain\Task;

/**
 * Task BroardcastThatMessageWasCreatedTask
 *
 * @property-read Message $message
 */
class BroadcastThatMessageWasCreatedTask extends Task
{
    public function handle(): self
    {
        MessageWasCreated::dispatch($this->message);

        return $this;
    }
}
