<?php

declare(strict_types = 1);

namespace App\Brain\Chat\Processes;

use App\Brain\Chat\Tasks\BroadcastThatMessageWasCreatedTask;
use App\Brain\Chat\Tasks\CreateMessageTask;
use Brain\Process;

class SendAPinggProcess extends Process
{
    protected array $tasks = [
        CreateMessageTask::class,
        BroadcastThatMessageWasCreatedTask::class,
    ];
}
