<?php

namespace App\Import;

use App\Import\Concerns\FormatProcessor;
use App\Import\Events\ImportSuccess;
use App\Import\Jobs\FileImportJob;
use Illuminate\Support\Facades\Redis;

class FileImportService
{
    private const REDIS_TOTAL_KEY_NAME = 'IMPORT_%s_TOTAL';
    private const REDIS_PROCESSED_KEY_NAME = 'IMPORT_%s_PROCESSED';


    public function import(string $filePath, FormatProcessor $processor): void
    {
        $id = $this->generateFileId($filePath);
        FileImportJob::dispatch($id, $processor)->onQueue('default');
    }


    public function startTracking(string $id, int $totalRowsCount): void
    {
        Redis::set(self::getTotalRowsCountRedisKeyName($id), $totalRowsCount);
        Redis::set(self::getProcessedRowsCountRedisKeyName($id), 0);
    }

    public final function getTotalRowsCount(string $id): int
    {
        return (int)Redis::get(self::getTotalRowsCountRedisKeyName($id));
    }

    public final function setTotalRowsCount(string $id, int $count): void
    {
        Redis::set(self::getTotalRowsCountRedisKeyName($id), $count);
    }

    public final function getProcessedRowsCount(string $id): int
    {
        return (int)Redis::get(self::getProcessedRowsCountRedisKeyName($id));
    }

    public function incrementProcessedRowsCount(string $id): void
    {
        Redis::incr(self::getProcessedRowsCountRedisKeyName($id), 1);
        if ($this->isFinished($id)) {
            $totalRowsCount = $this->getTotalRowsCount($id);
            $processedCount = $this->getProcessedRowsCount($id);
            broadcast(new ImportSuccess($id, $totalRowsCount, $processedCount))->toOthers();
            $this->clearImportCounter($id);
        }
    }

    public final function clearImportCounter(string $id): void
    {
        Redis::del(self::getTotalRowsCountRedisKeyName($id));
        Redis::del(self::getProcessedRowsCountRedisKeyName($id));
    }

    private static function getProcessedRowsCountRedisKeyName(string $id): string
    {
        return sprintf(self::REDIS_PROCESSED_KEY_NAME, $id);
    }

    private static function getTotalRowsCountRedisKeyName(string $id): string
    {
        return sprintf(self::REDIS_TOTAL_KEY_NAME, $id);
    }

    private function generateFileId(string $filePath): string
    {
        return md5(uniqid($filePath, true));
    }

    private function isFinished(string $id): bool
    {
        $totalRowsCount = $this->getTotalRowsCount($id);
        $processedCount = $this->getProcessedRowsCount($id);
        return $totalRowsCount === $processedCount;
    }
}
