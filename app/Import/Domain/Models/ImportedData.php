<?php

namespace App\Import\Domain\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportedData extends Model
{
    protected $table = 'imported_data';

    protected $primaryKey = 'row_id';

    protected $fillable = [
        'id',
        'name',
        'date'
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'date' => 'date',
    ];
}
