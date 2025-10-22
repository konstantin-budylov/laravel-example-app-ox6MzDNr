<?php

namespace App\Import\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $id;
    public string $message;
    public string $file;
    public string $line;

    /**
     * Create a new event instance.
     */
    public function __construct(string $fileId, \Throwable $e)
    {
        $this->id = $fileId;
        $this->message = $e->getMessage();
        $this->file = $e->getFile();
        $this->line = $e->getLine();
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
        ];
    }

    public function broadcastAs(): string
    {
        return 'import.failed';
    }
}
