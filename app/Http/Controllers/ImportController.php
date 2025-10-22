<?php

namespace App\Http\Controllers;

use App\Import\Domain\Formats\ImportedDataFormatProcessor;
use App\Import\FileImportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;

class ImportController extends Controller
{
    public function index()
    {
        return view('welcome');
    }
    public function upload(Request $request, FileImportService $fileImportService)
    {
        try {
            if (!$request->hasFile('import')) {
                throw new \RuntimeException('No file uploaded.');
            }

            $request->validate([
                'import' => [
                    'required',
                    'mimes:xlsx',
                    File::types(['xls', 'xlsx'])
                        ->max(5 * 1024), // 5MB max
                ],
            ]);
            $path = $request->file('import')?->store('imports', 'local');
            if ($path) {
                $fileImportService->import($path, new ImportedDataFormatProcessor($path, 'local'));
            } else {
                throw new \RuntimeException('Error occurs while uploading the file.');
            }
        } catch (\Throwable $e) {
            return back()->withErrors(['import' => $e->getMessage()]);
        }

        return back()->with('success', 'File uploaded successfully!');
    }
}
