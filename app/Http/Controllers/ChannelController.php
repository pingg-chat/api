<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Brain\Chat\Processes\SubscribeToAChannelProcess;
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
}
