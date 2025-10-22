<?php

namespace App\Import\Domain;

use App\Import\Domain\Models\ImportedData;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ImportedDataRepository
{
    public function getAllGroupedByDate(): Collection
    {
        $rows = ImportedData::all()
            ->select(['id','name','date'])
            ->groupBy('date')
            ->sortBy('date', SORT_ASC)
            ->all();
        $data = collect();
        foreach ($rows as $date => $items) {
            $date = Carbon::parse($date)->format('Y-m-d');
            $data->put($date, $items->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ];
            }));
        }
        return $data;
    }
}
