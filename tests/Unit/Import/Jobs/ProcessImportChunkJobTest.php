<?php

namespace Tests\Unit\Import\Jobs;

use App\Import\Domain\ImportedDataService;
use App\Import\Domain\Models\ImportedData;
use App\Import\FileImportService;
use App\Import\Jobs\ProcessImportChunkJob;
use Tests\TestCase;

class ProcessImportChunkJobTest extends TestCase
{
    public function test_handle_creates_models_and_increments_processed_count()
    {
        $row = ['id' => 1, 'name' => 'n', 'date' => '2020-01-01'];
        $collection = collect([$row]);

        // Используем реальный экземпляр ImportedData — он совместим с объявленным возвращаемым типом
        $model = new ImportedData($row);

        $mockImportService = $this->createMock(FileImportService::class);
        $mockImportService->expects($this->once())->method('incrementProcessedRowsCount')->with('file-id');

        $mockImportedDataService = $this->createMock(ImportedDataService::class);
        $mockImportedDataService->expects($this->once())->method('createFromArray')->with($row)->willReturn($model);

        $job = new ProcessImportChunkJob('file-id', $collection);

        // Вызываем handle напрямую, передавая mock-ы
        $job->handle($mockImportService, $mockImportedDataService);
    }

    public function test_handle_broadcasts_and_throws_on_row_processing_exception()
    {
        $this->expectException(\RuntimeException::class);

        $row = ['id' => 1, 'name' => 'n', 'date' => '2020-01-01'];
        $collection = collect([$row]);

        $mockImportService = $this->createMock(FileImportService::class);
        $mockImportedDataService = $this->createMock(ImportedDataService::class);

        $mockImportedDataService->expects($this->once())->method('createFromArray')->willThrowException(new \RuntimeException('row error'));

        $job = new ProcessImportChunkJob('file-id', $collection);

        $job->handle($mockImportService, $mockImportedDataService);
    }
}
