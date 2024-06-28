<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\FileSystem;

use App\Application\Spi\LogsReaderInterface;
use App\Infrastructure\ParserFormat\LogParserFormatInterface;
use Psr\Log\LoggerInterface;

readonly class LogsReader implements LogsReaderInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private LogParserFormatInterface $logParserFormat,
        private string $filePath,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getEntries(int $offset, int $limit): iterable
    {
        if (! file_exists($this->filePath)) {
            // Logging low-level technical specific information
            $this->logger->error("File '$this->filePath' does not exist");
            // Throw more general error for the application
            throw new \RuntimeException("Couldn't handle file");
        }

        $logFile = new \SplFileObject($this->filePath);
        $logFile->setFlags(\SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);

        $logsCollection = [];
        $lineCount = 0;

        $logFile->seek($offset);
        while (! $logFile->eof() && $lineCount < $limit) {
            $logsCollection[] = $this->logParserFormat->fromString($logFile->current());
            $logFile->next();
            $lineCount++;
        }
        $logFile->seek($offset);

        return $logsCollection;
    }
}
