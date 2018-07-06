<?php
namespace FroshProfiler\Components;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * Detailed debug information
     */
    const DEBUG = 100;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 200;

    /**
     * Uncommon events
     */
    const NOTICE = 250;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 300;

    /**
     * Runtime errors
     */
    const ERROR = 400;

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 500;

    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    const ALERT = 550;

    /**
     * Urgent alert.
     */
    const EMERGENCY = 600;

    /**
     * @var string[]
     */
    private static $levels = [
        self::DEBUG     => 'DEBUG',
        self::INFO      => 'INFO',
        self::NOTICE    => 'NOTICE',
        self::WARNING   => 'WARNING',
        self::ERROR     => 'ERROR',
        self::CRITICAL  => 'CRITICAL',
        self::ALERT     => 'ALERT',
        self::EMERGENCY => 'EMERGENCY',
    ];

    /**
     * @var LoggerInterface
     */
    private $originalLogger;

    /**
     * @var string
     */
    private $channelName;

    /**
     * @var array
     */
    private $messages = ['DEBUG' => [], 'OTHER' => []];

    /**
     * @param LoggerInterface $parentLogger
     * @param string $channelName
     */
    public function __construct(LoggerInterface $parentLogger, $channelName)
    {
        $this->originalLogger = $parentLogger;
        $this->channelName = ucfirst($channelName);
    }

    /**
     * @return array
     */
    public function getLoggedMessages()
    {
        return $this->messages;
    }

    /**
     * @inheritdoc
     */
    public function emergency($message, array $context = [])
    {
        $this->storeMessage(self::EMERGENCY, $message, $context);
        $this->originalLogger->emergency($message, $context);
    }

    /**
     * @inheritdoc
     */
    public function alert($message, array $context = [])
    {
        $this->storeMessage(self::ALERT, $message, $context);
        $this->originalLogger->alert($message, $context);
    }

    /**
     * @inheritdoc
     */
    public function critical($message, array $context = [])
    {
        $this->storeMessage(self::CRITICAL, $message, $context);
        $this->originalLogger->critical($message, $context);
    }

    /**
     * @inheritdoc
     */
    public function error($message, array $context = [])
    {
        $this->storeMessage(self::ERROR, $message, $context);
        $this->originalLogger->error($message, $context);
    }

    /**
     * @inheritdoc
     */
    public function warning($message, array $context = [])
    {
        $this->storeMessage(self::WARNING, $message, $context);
        $this->originalLogger->warning($message, $context);
    }

    /**
     * @inheritdoc
     */
    public function notice($message, array $context = [])
    {
        $this->storeMessage(self::NOTICE, $message, $context);
        $this->originalLogger->notice($message, $context);
    }

    /**
     * @inheritdoc
     */
    public function info($message, array $context = [])
    {
        $this->storeMessage(self::INFO, $message, $context);
        $this->originalLogger->info($message, $context);
    }

    /**
     * @inheritdoc
     */
    public function debug($message, array $context = [])
    {
        $this->storeMessage(self::DEBUG, $message, $context);
        $this->originalLogger->debug($message, $context);
    }

    /**
     * @inheritdoc
     */
    public function log($level, $message, array $context = [])
    {
        $this->storeMessage((int) $level, $message, $context);
        $this->originalLogger->log($level, $message, $context);
    }

    /**
     * @param int $level
     * @param string $message
     * @param array $context
     */
    private function storeMessage($level, $message, array $context = [])
    {
        $this->messages[$level > 100 ? 'OTHER' : 'DEBUG'][] =
            [
                self::getLevelName($level),
                $message,
                $context,
                time(),
                $this->channelName
            ];
    }

    /**
     * Gets the name of the logging level.
     * Copied from https://github.com/Seldaek/monolog/blob/master/src/Monolog/Logger.php
     * Jordi Boggiano - j.boggiano@seld.be - http://twitter.com/seldaek
     * MIT License at time of writing, i.e. https://github.com/Seldaek/monolog/commit/50de6999
     *
     * @throws \Psr\Log\InvalidArgumentException If level is not defined
     */
    private static function getLevelName($level)
    {
        if (!isset(static::$levels[$level])) {
            throw
                new InvalidArgumentException(
                    'Level "'.$level.'" is not defined, use one of: ' .
                    implode(', ', array_keys(static::$levels))
                );
        }

        return static::$levels[$level];
    }
}
