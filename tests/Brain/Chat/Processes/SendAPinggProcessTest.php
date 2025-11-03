<?php

declare(strict_types = 1);

use App\Brain\Chat\Processes\SendAPinggProcess;
use App\Brain\Chat\Tasks\BroadcastThatMessageWasCreatedTask;
use App\Brain\Chat\Tasks\CreateMessageTask;

test('check list of tasks', function (): void {
    $process = new SendAPinggProcess();

    expect($process->getTasks())
        ->toBe([
            CreateMessageTask::class,
            BroadcastThatMessageWasCreatedTask::class,
        ]);
});
