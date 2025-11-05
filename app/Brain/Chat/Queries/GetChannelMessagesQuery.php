<?php

declare(strict_types = 1);

namespace App\Brain\Chat\Queries;

use App\Models\Message;
use Brain\Query;
use Illuminate\Support\Collection;
use stdClass;

class GetChannelMessagesQuery extends Query
{
    public function __construct(
        protected readonly int $channelId,
        protected readonly ?int $threadId = null,
    ) {
        //
    }

    public function handle(): Collection | stdClass
    {
        return Message::query()
            ->with('user')
            ->where('channel_id', $this->channelId)
            ->when(
                $this->threadId,
                fn ($q) => $q->where('thread_id', $this->threadId),
                fn ($q) => $q->whereNull('thread_id')
            )
            ->get();
    }
}
