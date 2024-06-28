<?php

declare(strict_types=1);

namespace App\Infrastructure\ParserFormat;

use App\Application\LogEntryDto;

interface LogParserFormatInterface
{
    /**
     * Strategy contract for parsing the specific log entry format.
     *
     * @param string $logEntry
     * @return LogEntryDto|null
     */
    public function fromString(string $logEntry): ?LogEntryDto;
}
