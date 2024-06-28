<?php

declare(strict_types=1);

namespace App\Application\Spi;

interface LogIteratorStateInterface
{
    /**
     * Gets current offset for the log file.
     *
     * @return int
     */
    public function offset(): int;

    /**
     * Start exclusive processing of the log file.
     *
     * @return bool return false in case of another process is already running.
     */
    public function start(): bool;

    /**
     * Finalize processing the log file
     *
     * @param int $currentOffset line number after processing iteration.
     *
     * @return void
     */
    public function stop(int $currentOffset): void;
}
