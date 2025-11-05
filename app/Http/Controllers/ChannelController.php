<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Brain\Chat\Processes\SubscribeToAChannelProcess;
use App\Brain\Chat\Queries\GetChannelMessagesQuery;
use App\Http\Resources\MessageResource;
use Illuminate\Support\Facades\Auth;

class ChannelController
{
    public function subscribe(int $channel)
    {
        SubscribeToAChannelProcess::dispatch([
            'user_id'    => Auth::id(),
            'channel_id' => $channel,
        ]);

        return response()->noContent();
    }

    public function messages(int $channel, ?int $thread = null)
    {
        $messages = GetChannelMessagesQuery::run(channelId: $channel, threadId: $thread);

        return MessageResource::collection($messages);
    }
}
