<?php

namespace Tests\Unit\Import\Jobs;

use App\Import\Domain\ImportedDataService;
use App\Import\FileImportService;
use App\Import\Jobs\ProcessImportChunkJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessImportChunkJobDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_persists_rows_to_database_and_increments_counter()
    {
        $row = ['id' => 1, 'name' => 'n', 'date' => '2020-01-01'];
        $collection = collect([$row]);

        // реальный сервис, который сохранит модель в БД
        $importedDataService = new ImportedDataService();

        // мок сервиса трекинга, ожидаем вызов incrementProcessedRowsCount
        $mockFileImportService = $this->createMock(FileImportService::class);
        $mockFileImportService->expects($this->once())
            ->method('incrementProcessedRowsCount')
            ->with('file-id');

        $job = new ProcessImportChunkJob('file-id', $collection);

        // Выполняем обработчик с моком и реальным сервисом
        $job->handle($mockFileImportService, $importedDataService);

        // Проверяем, что данные действительно записаны в таблицу imported_data
        $this->assertDatabaseHas('imported_data', [
            'id' => 1,
            'name' => 'n',
            'date' => '2020-01-01',
        ]);
    }
}
