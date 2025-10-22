<?php

namespace Tests\Unit\Import\Domain\Formats;

use App\Import\Domain\Formats\ImportedDataFormatProcessor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportedDataFormatProcessorTest extends TestCase
{
    public function test_import_to_collection_parses_xlsx_correctly()
    {
        $src = base_path('tests/data/test.xlsx');
        $dstRelative = 'tests/test.xlsx';

        // Сохраняем фикстуру через Storage::disk('local') чтобы файл реально был видим диску "local"
        Storage::disk('local')->put($dstRelative, file_get_contents($src));

        try {
            $processor = new ImportedDataFormatProcessor($dstRelative, 'local');

            $collection = $processor->importToCollection();

            $this->assertInstanceOf(Collection::class, $collection);
            $this->assertNotEmpty($collection->toArray(), 'Collection should not be empty for provided fixture');

            foreach ($collection as $row) {
                $this->assertIsArray($row, 'Each row must be an array');
                $this->assertArrayHasKey('id', $row);
                $this->assertArrayHasKey('name', $row);
                $this->assertArrayHasKey('date', $row);

                $this->assertIsInt($row['id'], 'id must be integer');
                $this->assertIsString($row['name'], 'name must be string');

                // date in Y-m-d format
                $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $row['date']);
            }
        } finally {
            // Удаляем временный файл через Storage
            Storage::disk('local')->delete($dstRelative);
        }
    }
}
