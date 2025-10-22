<?php

namespace App\Import\Domain;

use App\Import\Domain\Models\ImportedData;

class ImportedDataService
{
    /**
     * @param string $path
     * @return void
     * @throws \Exception
     */
    public function createFromArray(array $item): ImportedData
    {
        $model = new ImportedData([
            'id' => $item['id'],
            'name' => $item['name'],
            'date' => $item['date'],
        ]);
        if (!$model->save()) {
            throw new \RuntimeException('Failed to create model ['.json_encode($model->attributes).']');
        }
        return $model;
    }
}
