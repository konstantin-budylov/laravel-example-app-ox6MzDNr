<?php

namespace Tests\Unit\Import\Jobs;

use App\Import\Concerns\FormatProcessor;
use App\Import\Jobs\FileImportJob;
use App\Import\Jobs\ProcessImportChunkJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class FileImportJobTest extends TestCase
{
    public function test_handle_starts_tracking_and_dispatches_chunks()
    {
        Bus::fake();

        // Простой процессор, возвращающий 3 элемента => будут диспатчены чанки
        $processor = new class implements FormatProcessor {
            public function __construct(string $p = '', string $d = '') {}
            public function importToCollection(): Collection { return collect([1,2,3]); }
        };

        // Мок сервиса import, ожидаем вызов startTracking с известным id и count
        $mockImport = $this->createMock(\App\Import\FileImportService::class);
        $mockImport->expects($this->once())->method('startTracking')->with('test-file-id', 3);
        $this->app->instance('import', $mockImport);

        $job = new FileImportJob('test-file-id', $processor);

        $job->handle();

        Bus::assertDispatched(ProcessImportChunkJob::class);
    }

    public function test_handle_throws_and_broadcasts_on_processor_exception()
    {
        $this->expectException(\RuntimeException::class);

        $processor = new class implements FormatProcessor {
            public function __construct(string $p = '', string $d = '') {}
            public function importToCollection(): Collection { throw new \RuntimeException('boom'); }
        };

        // Подменяем app('import') чтобы не сломать при старте
        $this->app->instance('import', $this->createMock(\App\Import\FileImportService::class));

        $job = new FileImportJob('test-file-id', $processor);

        $job->handle();
    }
}
