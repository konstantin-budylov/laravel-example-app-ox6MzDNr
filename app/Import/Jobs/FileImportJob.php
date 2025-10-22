<?php

namespace App\Import\Jobs;

use App\Import\Concerns\FormatProcessor;
use App\Import\Events\ImportFailed;
use App\Import\Events\ImportStarted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FileImportJob implements ShouldQueue
{
    use Queueable;
    const CHUNK_SIZE = 1000;

    public $tries = 1;
    public string $id;
    public FormatProcessor $processor;

    /**
     * Create a new job instance.
     */
    public function __construct(string $id, FormatProcessor $processor)
    {
        $this->id = $id;
        $this->processor = $processor;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $data = $this->processor->importToCollection();
            app('import')->startTracking($this->id, $data->count());
            broadcast(new ImportStarted($this->id, $data->count()))->toOthers();
            foreach ($data->chunk(self::CHUNK_SIZE) as $chunk) {
                ProcessImportChunk::dispatch($this->id, $chunk);
            }
        } catch (\Exception $e) {
            broadcast(new ImportFailed($this->id, $e))->toOthers();
            throw $e;
        }
    }
}
