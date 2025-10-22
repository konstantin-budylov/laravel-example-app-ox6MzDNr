<?php

namespace Tests\Unit\Import;

use App\Import\Concerns\FormatProcessor;
use App\Import\FileImportService;
use App\Import\Jobs\FileImportJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class FileImportServiceTest extends TestCase
{
    public function test_import_dispatches_file_import_job()
    {
        Bus::fake();

        $service = new FileImportService();

        $processor = new class('some/path', 'local') implements FormatProcessor {
            public function __construct(string $p, string $d) {}
            public function importToCollection(): Collection { return collect(); }
        };

        $service->import('some/path', $processor);

        Bus::assertDispatched(FileImportJob::class, function ($job) use ($processor) {
            return $job->processor instanceof \App\Import\Concerns\FormatProcessor;
        });
    }

    public function test_start_tracking_and_increment_uses_redis_and_clears_on_finish()
    {
        // Ожидаем вызовы Redis
        Redis::shouldReceive('set')->twice()->andReturnTrue();
        // get будет вызван несколько раз; первый для getTotalRowsCount, затем для getProcessedRowsCount
        Redis::shouldReceive('get')->andReturnUsing(function ($key) {
            // вернуть одинаковое значение чтобы симулировать завершение
            return 5;
        });
        Redis::shouldReceive('incr')->once()->andReturn(5);
        Redis::shouldReceive('del')->twice()->andReturnTrue();

        $service = new FileImportService();

        $service->startTracking('file-id', 5);

        $this->assertSame(5, $service->getTotalRowsCount('file-id'));
        // инкремент должен вызвать clearImportCounter если станет завершено
        $service->incrementProcessedRowsCount('file-id');
    }
}
