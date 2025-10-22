<?php

namespace App\Import\Concerns;

use Illuminate\Support\Collection;

interface FormatProcessor
{
    public function __construct(string $path, string $disk);

    public function importToCollection(): Collection;
}
