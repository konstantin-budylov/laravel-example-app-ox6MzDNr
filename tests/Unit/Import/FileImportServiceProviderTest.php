<?php

namespace Tests\Unit\Import;

use App\Import\FileImportService;
use Tests\TestCase;

class FileImportServiceProviderTest extends TestCase
{
    public function test_import_singleton_is_registered_in_container()
    {
        $instance = $this->app->make('import');

        $this->assertInstanceOf(FileImportService::class, $instance);
        // тот же экземпляр при повторном получении
        $this->assertSame($instance, $this->app->make('import'));
    }
}
