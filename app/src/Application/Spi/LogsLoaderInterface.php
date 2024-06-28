<?php

declare(strict_types=1);

namespace App\Application\Spi;

interface LogsLoaderInterface
{
    /**
     * Load log entries by given criteria.
     *
     * @param string[] $serviceNameCollection
     * @param \DateTimeImmutable|null $startDate
     * @param \DateTimeImmutable|null $endDate
     * @param int|null $statusCode
     * @return int
     * @throws \RuntimeException
     */
    public function countByCriteria(
        iterable $serviceNameCollection,
        ?\DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate,
        ?int $statusCode,
    ): int;
}
