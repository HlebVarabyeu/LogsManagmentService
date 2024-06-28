<?php

namespace App\Tests\Infrastructure;

use App\Application\LogEntryDto;
use App\Infrastructure\ParserFormat\AccessLogFormat;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AccessLogFormatTest extends TestCase
{
    public function testFromString(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $parser = new AccessLogFormat($loggerMock);

        $logEntry = '[example.com] - - [10/May/2023:12:34:56 +0000] "GET /page" 200';
        $result = $parser->fromString($logEntry);

        $this->assertInstanceOf(LogEntryDto::class, $result);
        $this->assertEquals('[example.com]', $result->serviceNames);
        $this->assertEquals(200, $result->statusCode);
    }
}