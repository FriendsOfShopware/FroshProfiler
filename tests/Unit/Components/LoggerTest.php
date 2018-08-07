<?php

use FroshProfiler\Components\Logger;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public function testAddRecordAtLevelDebugStoresMessageInDebug()
    {
        $logger =
            new Logger(
                'someChannel'
            );
        $logger->debug('A message with level 100');

        $this->assertEquals(
            [
                'DEBUG' => [[
                        'DEBUG',
                        'A message with level 100',
                        [],
                        time(),
                        'SomeChannel',
                    ]],
                'OTHER' => [],
            ],
            $logger->getLoggedMessages()
        );
    }

    public function testAddRecordAtLevelInfoStoresMessageToOther()
    {
        $logger =
            new Logger(
                'someChannel'
            );
        $logger->info('A message with level 200');

        $this->assertEquals(
            [
                'DEBUG' => [],
                'OTHER' => [[
                        'INFO',
                        'A message with level 200',
                        [],
                        time(),
                        'SomeChannel',
                    ]],
            ],
            $logger->getLoggedMessages()
        );
    }

    /**
     * @param $level
     * @param $message
     * @dataProvider allLevelsDataProvider
     */
    public function testAllLogLevels($level, $message)
    {
        $logger =
            new Logger(
                'someChannel'
            );
        $logger->$level($message);

        if ($level === 'debug') {
            $this->assertEquals(
                [
                    'DEBUG' => [[
                            strtoupper($level),
                            $message,
                            [],
                            time(),
                            'SomeChannel',
                        ]],
                    'OTHER' => [],
                ],
                $logger->getLoggedMessages()
            );
        } else {
            $this->assertEquals(
                [
                    'DEBUG' => [],
                    'OTHER' => [
                        [
                            strtoupper($level),
                            $message,
                            [],
                            time(),
                            'SomeChannel',
                        ],
                    ],
                ],
                $logger->getLoggedMessages()
            );
        }
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
