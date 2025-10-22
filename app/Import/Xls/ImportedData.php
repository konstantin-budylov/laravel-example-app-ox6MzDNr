<?php

namespace App\Import\Xls;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportedData implements ToArray, WithHeadingRow
{
    public function array(array $array)
    {
        return [
            'id' => $array['id'],
            'name' => $array['name'],
            'date' => Carbon::parse($array['date'])?->toDateString(),
        ];
    }
}
