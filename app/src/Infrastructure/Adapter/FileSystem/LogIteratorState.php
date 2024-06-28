<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\FileSystem;

use App\Application\Spi\LogIteratorStateInterface;
use Psr\Log\LoggerInterface;

class LogIteratorState implements LogIteratorStateInterface
{
    private $stream;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $tmpDirPath,
        private readonly string $logFilePath,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function start(): bool
    {
        $this->stream = fopen($this->tmpDirPath . md5($this->logFilePath), 'c+');
        if ($this->stream === false) {
            $this->logger->error("Couldn't open offset file");
            return false;
        }
        if (! flock($this->stream, LOCK_EX | LOCK_NB)) {
            fclose($this->stream);
            $this->logger->error("Couldn't acquire exclusive file lock");
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function offset(): int
    {
        if (! $this->stream) {
            throw new \RuntimeException("No file stream available");
        }
        fseek($this->stream, 0);
        $content = fread($this->stream, filesize($this->logFilePath));
        if ($content === false) {
            $this->logger->error("Couldn't read offset from the file");
            throw new \RuntimeException("File system error");
        }
        return (int) $content;
    }

    /**
     * @inheritDoc
     */
    public function stop(int $currentOffset): void
    {
        if (! $this->stream) {
            throw new \RuntimeException("No file stream available");
        }
        $this->writeOffset($currentOffset);
        fclose($this->stream);
    }

    /**
     * @param int $offset
     * @return void
     */
    private function writeOffset(int $offset): void
    {
        if (! $this->stream) {
            throw new \RuntimeException("No file stream available");
        }
        fseek($this->stream, 0);
        ftruncate($this->stream, 0);
        if (fwrite($this->stream, (string) $offset) === false) {
            $this->logger->error("Couldn't write offset from the file");
            throw new \RuntimeException("File system error");
        }
        fflush($this->stream);
    }
}
