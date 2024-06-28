<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\MySql;

use App\Application\Spi\LogsLoaderInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;

readonly class LogsLoader implements LogsLoaderInterface
{
    public function __construct(private Connection $connection, private LoggerInterface $logger,)
    {
    }

    /**
     * @inheritDoc
     */
    public function countByCriteria(
        iterable $serviceNameCollection,
        ?\DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate,
        ?int $statusCode,
    ): int {
        $params = [];
        $whereConditions = [];
        if (! empty($serviceNameCollection)) {
            $serviceNameParams = [];
            foreach ($serviceNameCollection as $key => $serviceName) {
                $serviceNameParams[] = ":serviceName$key";
                $params["serviceName$key"] = $serviceName;
            }
            $whereConditions[] = "service_name IN (" . implode(", ", $serviceNameParams) . ")";
        }
        if ($statusCode !== null) {
            $whereConditions[] = "code = :statusCode";
            $params['statusCode'] = $statusCode;
        }
        if ($startDate !== null) {
            $whereConditions[] = "date_time >= :startDate";
            $params['startDate'] = $startDate->format('Y-m-d H:i:s');
        }
        if ($endDate !== null) {
            $whereConditions[] = "date_time <= :endDate";
            $params['endDate'] = $endDate->format('Y-m-d H:i:s');
        }

        $sql = "SELECT COUNT(*) FROM log";
        if (! empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        try {
            return $this->connection->executeQuery($sql, $params)->fetchOne();
        } catch (Exception $e) {
            $this->logger->error('Count Mysql query failed: ' . $e->getMessage());
            throw new \RuntimeException('Logs counting failed' . $e->getMessage());
        }
    }
}
