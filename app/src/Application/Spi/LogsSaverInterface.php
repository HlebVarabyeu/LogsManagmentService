<?php

declare(strict_types=1);

namespace App\Application\Spi;

use App\Application\LogEntryDto;

interface LogsSaverInterface
{
    /**
     * Save log entries to DB storage.
     *
     * @param LogEntryDto[] $logsCollection
     * @return void
     * @throws \RuntimeException
     */
    public function persist(iterable $logsCollection): void;
}
