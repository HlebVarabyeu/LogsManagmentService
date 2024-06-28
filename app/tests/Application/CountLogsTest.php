<?php

namespace App\Tests\Application;

use App\Application\Spi\LogsLoaderInterface;
use PHPUnit\Framework\TestCase;
use App\Application\CountLogs;

class CountLogsTest extends TestCase
{
    public function testCount(): void
    {
        $logsLoaderMock = $this->createMock(LogsLoaderInterface::class);

        $logsLoaderMock->expects($this->once())
            ->method('countByCriteria')
            ->willReturn(42);

        $countLogs = new CountLogs($logsLoaderMock);

        $result = $countLogs->process([], null, null, 200);

        $this->assertEquals(42, $result);
    }
}