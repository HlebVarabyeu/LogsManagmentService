<?php

namespace App\Tests\Application;

use App\Application\AcquireLogs;
use App\Application\Spi\LogIteratorStateInterface;
use App\Application\Spi\LogsReaderInterface;
use App\Application\Spi\LogsSaverInterface;
use PHPUnit\Framework\TestCase;

class AcquireLogsTest extends TestCase
{
    public function testProcess(): void
    {
        $logIteratorStateMock = $this->createMock(LogIteratorStateInterface::class);
        $logsReaderMock = $this->createMock(LogsReaderInterface::class);
        $logsSaverMock = $this->createMock(LogsSaverInterface::class);

        $logIteratorStateMock->expects($this->once())
            ->method('start')
            ->willReturn(true);
        $logIteratorStateMock->expects($this->once())
            ->method('offset')
            ->willReturn(0);
        $logsReaderMock->expects($this->once())
            ->method('getEntries')
            ->with(0, 10)
            ->willReturn([]);
        $logsSaverMock->expects($this->once())
            ->method('persist')
            ->with([]);
        $logIteratorStateMock->expects($this->once())
            ->method('stop')
            ->with(0);

        $acquireLogs = new AcquireLogs($logIteratorStateMock, $logsReaderMock, $logsSaverMock);

        $result = $acquireLogs->process(10);

        $this->assertEquals(0, $result);
    }
}