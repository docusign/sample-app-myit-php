<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class EnvelopeEvent
 *
 * @package App\Events
 */
class EnvelopeEvent implements ShouldBroadcast
{
    /**
     * @param string $tokenId
     * @param string $user
     * @param string $createdAt
     */
    public function __construct(
        protected string $event,
        protected string $tokenId,
        public string $user,
        public string $createdAt
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel("channel.{$this->tokenId}");
    }

    /**
     * The event's broadcast name
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return $this->event;
    }
}