<?php

namespace FroshProfiler\Components;

use Monolog\Handler\HandlerInterface;
use Shopware\Components\Logger as BaseLogger;

class Logger extends BaseLogger
{
    /**
     * @var string
     */
    private $channelName;

    /**
     * @var array
     */
    private $messages = ['DEBUG' => [], 'OTHER' => []];

    /**
     * @param string             $name       The logging channel
     * @param HandlerInterface[] $handlers   optional stack of handlers, the first one in the array is called first, etc
     * @param callable[]         $processors Optional array of processors
     */
    public function __construct($name, $handlers = [], $processors = [])
    {
        parent::__construct($name, $handlers, $processors);
        $this->channelName = ucfirst($name);
    }

    /**
     * @return array
     */
    public function getLoggedMessages()
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function emergency($message, array $context = [])
    {
        $this->storeMessage(self::EMERGENCY, $message, $context);
        parent::emergency($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function alert($message, array $context = [])
    {
        $this->storeMessage(self::ALERT, $message, $context);
        parent::alert($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, array $context = [])
    {
        $this->storeMessage(self::CRITICAL, $message, $context);
        parent::critical($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, array $context = [])
    {
        $this->storeMessage(self::ERROR, $message, $context);
        parent::error($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, array $context = [])
    {
        $this->storeMessage(self::WARNING, $message, $context);
        parent::warning($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, array $context = [])
    {
        $this->storeMessage(self::NOTICE, $message, $context);
        parent::notice($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $context = [])
    {
        $this->storeMessage(self::INFO, $message, $context);
        parent::info($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = [])
    {
        $this->storeMessage(self::DEBUG, $message, $context);
        parent::debug($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = [])
    {
        $this->storeMessage((int) $level, $message, $context);
        parent::log($level, $message, $context);
    }

    /**
     * @param int    $level
     * @param string $message
     * @param array  $context
     */
    private function storeMessage($level, $message, array $context = [])
    {
        $this->messages[$level > 100 ? 'OTHER' : 'DEBUG'][] =
            [
                self::getLevelName($level),
                $message,
                $context,
                time(),
                $this->channelName,
            ];
    }
}
