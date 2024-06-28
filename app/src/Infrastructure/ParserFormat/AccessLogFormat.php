<?php

declare(strict_types=1);

namespace App\Infrastructure\ParserFormat;

use App\Application\LogEntryDto;
use Psr\Log\LoggerInterface;

class AccessLogFormat implements LogParserFormatInterface
{
    private const string PATTERN = '/(?<name>[^\s]+) - - \[(?<dateTime>[^\]]+)\] "[^"]*" (?<code>\d+)/';

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * @inheritDoc
     */
    public function fromString(string $logEntry): ?LogEntryDto
    {
        if (preg_match($this::PATTERN, $logEntry, $matches)) {
            $serviceNames = $matches['name'];
            $statusCode = (int) $matches['code'];
            $dateTime = \DateTimeImmutable::createFromFormat('d/M/Y:H:i:s O', $matches['dateTime']);

            return new LogEntryDto($serviceNames, $statusCode, $dateTime);
        } else {
            $this->logger->error('A log entry does\'t conform to the format');
            // Return NULL in case of an unreadable entry just to count the line as processed
            return null;
        }
    }
}
