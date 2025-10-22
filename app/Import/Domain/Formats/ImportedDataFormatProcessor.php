<?php

namespace App\Import\Domain\Formats;

use App\Import\Concerns\FormatProcessor;
use App\Import\Xls\ImportedData;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Laravel\Reverb\Loggers\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportedDataFormatProcessor implements FormatProcessor
{
    private string $path;
    private string $disk;
    public function __construct(string $path, string $disk)
    {
        $this->path = $path;
        $this->disk = $disk;
    }

    public final function importToCollection(): Collection
    {
        try {
            //Importing data into collection (by sheets)
            $sheets = Excel::toCollection(new ImportedData(), $this->path, $this->disk);
            /** @var Collection $sheet */
            $rows = collect();
            //Collecting all processed sheets data into one collection
            foreach ($sheets as $sheet) {
                //Cleanup and parse data
                $rows = $rows->concat($this->parseImportData($sheet));
            }
            return $rows;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private function parseImportData(Collection $sheet): array
    {
        return $sheet->map(function ($row) {
            return $this->parseRowData($row);
        })->filter(function ($row) {
            return !empty($row);
        })->toArray();
    }

    private function parseRowData(Collection $item): array
    {
        $row = $item->toArray();
        if (
            !empty($row['id'])
            && !empty($row['name'])
            && !empty($row['date'])
        ) {
            preg_match('/^\D*(\d+)\D*\d*$/i', $row['id'], $match);
            return [
                'id' => (int)$match[1],
                'name' => $row['name'],
                'date' => Carbon::parse($row['date'])?->toDateString(),
            ];
        }
        Log::error('ImportedDataFormatProcessor: invalid row data', ['row' => $row]);
        return [];
    }
}
