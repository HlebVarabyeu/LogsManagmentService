<?php

declare(strict_types=1);

namespace App\Application;

use DateTimeImmutable;

/**
 * DTO object with public readonly properties to pass log entry between layers.
 */
readonly class LogEntryDto
{
    public function __construct(
        public string $serviceNames,
        public int $statusCode,
        public ?DateTimeImmutable $dateTime,
    ) {
    }
}
