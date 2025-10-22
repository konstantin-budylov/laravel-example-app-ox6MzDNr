<?php

namespace App\Import\Events;

use App\Import\Domain\Models\ImportedData;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportRowSuccess implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $id;
    public string $attributes;
    /**
     * Create a new event instance.
     */
    public function __construct(string $id, string $attributes)
    {
        $this->id = $id;
        $this->attributes = $attributes;
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
            'attributes' => $this->attributes,
        ];
    }

    public function broadcastAs(): string
    {
        return 'import.row.success';
    }
}
