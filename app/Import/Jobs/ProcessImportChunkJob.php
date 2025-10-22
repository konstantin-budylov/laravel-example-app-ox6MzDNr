<?php

namespace App\Import\Jobs;

use App\Import\Domain\ImportedDataService;
use App\Import\Events\ImportRowFailed;
use App\Import\Events\ImportRowSuccess;
use App\Import\FileImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ProcessImportChunkJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private Collection $rows;
    private string $fileId;

    private int $totalCount = 0;

    public $tries = 1;

    public function __construct(string $fileId, Collection $rows)
    {
        $this->rows = $rows;
        $this->fileId = $fileId;
    }

    public function handle(FileImportService $fileImportService, ImportedDataService $importService)
    {
        try {
            foreach ($this->rows as $row) {
                $model = $importService->createFromArray($row);
                $fileImportService->incrementProcessedRowsCount($this->fileId);
                broadcast(new ImportRowSuccess($this->fileId, $model?->toJson()))->toOthers();
            }
        } catch (\Throwable $e) {
            broadcast(new ImportRowFailed($this->fileId, $e, $row))->toOthers();
            throw $e;
        }
    }
}
