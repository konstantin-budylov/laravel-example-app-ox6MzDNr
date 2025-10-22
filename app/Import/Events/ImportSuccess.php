<?php

namespace App\Import\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportSuccess implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $id;
    public int $totalRowsCount;
    public int $processedRowsCount;

    /**
     * Create a new event instance.
     */
    public function __construct(string $id, int $totalRowsCount, int $processedRowsCount)
    {
        $this->id = $id;
        $this->totalRowsCount = $totalRowsCount;
        $this->processedRowsCount = $processedRowsCount;
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
            'totalRowsCount' => $this->totalRowsCount,
            'processedRowsCount' => $this->processedRowsCount,
        ];
    }

    public function broadcastAs(): string
    {
        return 'import.success';
    }
}
