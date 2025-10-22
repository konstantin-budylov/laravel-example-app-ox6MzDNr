<?php

namespace App\Import\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $id;
    public int $count;

    /**
     * Create a new event instance.
     */
    public function __construct(string $id, int $count)
    {
        $this->id = $id;
        $this->count = $count;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('imports'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->id,
            'count' => $this->count,
        ];
    }

    public function broadcastAs(): string
    {
        return 'imports.started';
    }
}
