<?php

namespace App\Import\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportRowFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $id;
    public string $message;
    public string $file;
    public int $line;
    public array $attributes;

    /**
     * Create a new event instance.
     */
    public function __construct(string $id, \Throwable $exception, array $attributes)
    {
        $this->id = $id;
        $this->message = $exception->getMessage();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
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
            'message' => $this->message,
            'file' => $this->file,
            'line' => $this->line,
            'attributes' => $this->attributes,
        ];
    }

    public function broadcastAs(): string
    {
        return 'import.row.failed';
    }
}
