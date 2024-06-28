<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\MySql;

use App\Application\Spi\LogsSaverInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;

readonly class LogsSaver implements LogsSaverInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private Connection $connection,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function persist(iterable $logsCollection): void
    {
        try {
            $stmt = $this->connection->prepare("INSERT INTO log (service_name, code, date_time) VALUES (?, ?, ?)");
            $this->connection->beginTransaction();
            foreach ($logsCollection as $log) {
                if ($log) {
                    $stmt->executeStatement([
                        $log->serviceNames,
                        $log->statusCode,
                        $log->dateTime->format('Y-m-d H:i:s'),
                    ]);
                }
            }
            $this->connection->commit();
        } catch (Exception $e) {
            $this->logger->error('Log entry save failed: ' . $e->getMessage());
            throw new \RuntimeException('Could\'t save log entry');
        }
    }
}
