<?php

declare(strict_types=1);

namespace App\Application\Spi;

use App\Application\LogEntryDto;

interface LogsReaderInterface
{
    /**
     * Read fixed number of log entries from the log file.
     *
     * @param int $offset
     * @param int $limit
     * @return LogEntryDto[]
     * @throws \RuntimeException
     */
    public function getEntries(int $offset, int $limit): iterable;
}
