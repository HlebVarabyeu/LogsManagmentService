<?php

namespace App\Tests\Infrastructure;

use App\Application\CountLogs;
use App\Application\Spi\LogsLoaderInterface;
use App\Infrastructure\Adapter\Http\LogsCountController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogsCountControllerTest extends KernelTestCase
{
    private $logsLoaderMock;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->logsLoaderMock = $this->createMock(LogsLoaderInterface::class);
    }

    private function createController(): LogsCountController
    {
        $countLogs = new CountLogs($this->logsLoaderMock);
        $controller = new LogsCountController($countLogs);
        $controller->setContainer(self::getContainer());

        return $controller;
    }

    public function testIndexValidRequest(): void
    {
        $this->logsLoaderMock->method('countByCriteria')->willReturn(42);

        $controller = $this->createController();

        $request = new Request([
            'serviceNames' => ['service1', 'service2'],
            'startDate' => '2023-01-01T00:00:00+00:00',
            'endDate' => '2023-12-31T23:59:59+00:00',
            'statusCode' => 200,
        ]);

        $response = $controller->index($request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['counter' => 42]),
            $response->getContent()
        );
    }

    public function testIndexInvalidDateFormat(): void
    {
        $controller = $this->createController();

        $request = new Request([
            'startDate' => 'invalid-date-format',
        ]);

        $response = $controller->index($request);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['errors' => ['This value is not a valid datetime.']]),
            $response->getContent()
        );
    }

    public function testIndexWithEmptyRequest(): void
    {
        $this->logsLoaderMock->method('countByCriteria')->willReturn(0);

        $controller = $this->createController();

        $request = new Request();

        $response = $controller->index($request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['counter' => 0]),
            $response->getContent()
        );
    }
}