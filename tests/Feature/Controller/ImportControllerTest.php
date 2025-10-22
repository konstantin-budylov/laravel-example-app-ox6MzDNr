<?php

namespace Tests\Feature\Controller;

use App\Import\Domain\Formats\ImportedDataFormatProcessor;
use App\Import\FileImportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportControllerTest extends TestCase
{
    public function test_welcome_page_contains_form_elements()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Upload File');
        $response->assertSee('name="import"', false);
        $response->assertSee('Show data');
    }

    public function test_successful_upload_calls_service_and_stores_file()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('data.xlsx', 500, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Подставляем мок сервиса, ожидаем один вызов import(...)
        $mock = $this->createMock(FileImportService::class);
        $mock->expects($this->once())
            ->method('import')
            ->with(
                $this->isType('string'),
                $this->isInstanceOf(ImportedDataFormatProcessor::class)
            );

        $this->app->instance(FileImportService::class, $mock);

        $response = $this->post(route('upload'), ['import' => $file]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'File uploaded successfully!');

        // Проверяем, что файл записан на диск в папку imports
        Storage::disk('local')->assertExists('imports/' . $file->hashName());
    }

    public function test_upload_without_file_returns_validation_error()
    {
        $response = $this->post(route('upload'), []); // no file

        $response->assertRedirect();
        $response->assertSessionHasErrors('import');
    }

    public function test_upload_with_invalid_mime_returns_validation_error()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('not_excel.txt', 10, 'text/plain');

        $response = $this->post(route('upload'), ['import' => $file]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('import');
    }

    public function test_service_exception_returns_error_to_session()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('data.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $mock = $this->createMock(FileImportService::class);
        $mock->expects($this->once())
            ->method('import')
            ->willThrowException(new \RuntimeException('Import failed'));

        $this->app->instance(FileImportService::class, $mock);

        $response = $this->post(route('upload'), ['import' => $file]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('import');
        $this->assertStringContainsString('Import failed', session('errors')->first('import'));
    }
}
