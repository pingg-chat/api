<?php

declare(strict_types = 1);

namespace App\Brain\Chat\Tasks;

use App\Models\Message;
use Brain\Task;
use Illuminate\Validation\Rule;

/**
 * Task CreateMessageTask
 *
 * @property-read int $channelId
 * @property-read int $userId
 * @property-read string $content
 * @property-read int|null $threadId
 *
 * @property Message $message
 */
class CreateMessageTask extends Task
{
    public function rules(): array
    {
        return [
            'channelId' => ['required', 'integer', 'exists:channels,id'],
            'userId'    => [
                'required', 'integer', 'exists:users,id',
                Rule::exists('channel_user', 'user_id')
                    ->where('channel_id', $this->channelId),
            ],
            'content'  => ['required', 'string'],
            'threadId' => ['nullable', 'integer', 'exists:messages,id',
                Rule::exists('messages', 'id')
                    ->where('channel_id', $this->channelId),
            ],
        ];
    }

    public function handle(): self
    {
        $this->message = Message::create([
            'channel_id' => $this->channelId,
            'user_id'    => $this->userId,
            'content'    => $this->content,
            'thread_id'  => $this->threadId,
        ]);

        return $this;
    }
}
