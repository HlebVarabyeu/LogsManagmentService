<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Spi\LogsLoaderInterface;

readonly class CountLogs
{
    public function __construct(private readonly LogsLoaderInterface $logsLoader)
    {
    }

    /**
     * Calculate number of entries using given filters.
     *
     * @param iterable $serviceNameCollection
     * @param \DateTimeImmutable|null $startDate
     * @param \DateTimeImmutable|null $endDate
     * @param int|null $statusCode
     * @throws \RuntimeException
     * @return int
     */
    public function process(
        iterable $serviceNameCollection,
        ?\DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate,
        ?int $statusCode,
    ): int {
        return $this->logsLoader->countByCriteria($serviceNameCollection, $startDate, $endDate, $statusCode);
    }
}
