<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Spi\LogIteratorStateInterface;
use App\Application\Spi\LogsSaverInterface;
use App\Application\Spi\LogsReaderInterface;

readonly class AcquireLogs
{
    public function __construct(
        private LogIteratorStateInterface $logIteratorState,
        private LogsReaderInterface $logsReader,
        private LogsSaverInterface $logsSaver,
    ) {
    }

    /**
     * Start logs reading procedure.
     *
     * @param int $batchSize
     * @return int
     * @throws \RuntimeException
     */
    public function process(int $batchSize): int
    {
        if (! $this->logIteratorState->start()) {
            throw new \RuntimeException('The process is already running');
        }
        $offset = $this->logIteratorState->offset();

        $logsCollection = $this->logsReader->getEntries($offset, $batchSize);
        $this->logsSaver->persist($logsCollection);

        $linesProcessed = count($logsCollection);
        $this->logIteratorState->stop($offset + $linesProcessed);

        return $linesProcessed;
    }
}
