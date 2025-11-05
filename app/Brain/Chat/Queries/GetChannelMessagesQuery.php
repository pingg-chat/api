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
            ->select([
                'messages.id',
                'messages.content',
                'messages.created_at',
                'users.id as user_id',
                'users.name as user_name',
                'users.icon as user_icon',
                'users.username as user_username',
                'messages.thread_id',
            ])
            ->join('users', 'messages.user_id', '=', 'users.id')
            ->where('channel_id', $this->channelId)
            ->when(
                $this->threadId,
                fn ($q) => $q->where('thread_id', $this->threadId),
                fn ($q) => $q->whereNull('thread_id')
            )
            ->getQuery()
            ->get();
    }
}
