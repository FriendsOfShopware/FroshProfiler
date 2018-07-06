<?php

use FroshProfiler\Components\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LoggerTest extends TestCase
{
    public function testAddRecordAtLevelDebugStoresMessageInDebug()
    {
        $logger =
            new Logger(
                $this->createMock(LoggerInterface::class),
                'someChannel'
            );
        $logger->debug('A message with level 100');

        $this->assertEquals(
            [
                'DEBUG' =>
                    [[
                        'DEBUG',
                        'A message with level 100',
                        [],
                        time(),
                        'SomeChannel'
                    ]],
                'OTHER' => []
            ],
            $logger->getLoggedMessages()
        );
    }

    public function testAddRecordAtLevelInfoStoresMessageToOther()
    {
        $logger =
            new Logger(
                $this->createMock(LoggerInterface::class),
                'someChannel'
            );
        $logger->info('A message with level 200');

        $this->assertEquals(
            [
                'DEBUG' => [],
                'OTHER' =>
                    [[
                        'INFO',
                        'A message with level 200',
                        [],
                        time(),
                        'SomeChannel'
                    ]]
            ],
            $logger->getLoggedMessages()
        );
    }

    public function testLogCallDelegatesToOriginal()
    {
        $mockDecorated = $this->createMock(LoggerInterface::class);
        $mockDecorated->expects($this->once())->method('log')->with(250, 'message', ['context']);
        $logger =
            new Logger(
                $mockDecorated,
                'someChannel'
            );
        $logger->log(250, 'message', ['context']);
    }

    /**
     * @param $level
     * @param $message
     * @dataProvider allLevelsDataProvider
     */
    public function testEveryInterfaceBackedCallDelegatesToOriginal($level, $message)
    {
        $mockDecorated = $this->createMock(LoggerInterface::class);
        $mockDecorated->expects($this->once())->method($level)->with($message);
        $logger =
            new Logger(
                $mockDecorated,
                'someChannel'
            );
        $logger->$level($message);
    }

    public function allLevelsDataProvider()
    {
        return [
            ['debug', 'A debug Message'],
            ['info', 'An info Message'],
            ['notice', 'A notice Message'],
            ['warning', 'A warning Message'],
            ['error', 'An error Message'],
            ['critical', 'A critical message'],
            ['alert', 'An alert Message'],
            ['emergency', 'An emergency Message'],
        ];
    }
}
