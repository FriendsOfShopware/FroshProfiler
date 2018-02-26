<?php

namespace ShyimProfiler\Components;

use Shopware\Components\Logger as BaseLogger;

/**
 * Class Logger
 */
class Logger extends BaseLogger
{
    /**
     * @var BaseLogger
     */
    private $parentLogger;

    /**
     * @var string
     */
    private $channelName;

    /**
     * @var array
     */
    private $messages = [];

    /**
     * Logger constructor.
     *
     * @param BaseLogger                                $parentLogger
     * @param array|\Monolog\Handler\HandlerInterface[] $channelName
     */
    public function __construct(BaseLogger $parentLogger, $channelName)
    {
        $this->channelName = ucfirst($channelName);
        $this->parentLogger = $parentLogger;

        $this->messages['DEBUG'] = [];
        $this->messages['OTHER'] = [];
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->parentLogger, $name], $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function addRecord($level, $message, array $context = [])
    {
        $this->messages[$level > 100 ? 'OTHER' : 'DEBUG'][] = [static::getLevelName($level), $message, $context, time(), $this->channelName];

        return $this->parentLogger->addRecord($level, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function addDebug($message, array $context = [])
    {
        return $this->addRecord(static::DEBUG, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function addInfo($message, array $context = [])
    {
        return $this->addRecord(static::INFO, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function addNotice($message, array $context = [])
    {
        return $this->addRecord(static::NOTICE, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function addWarning($message, array $context = [])
    {
        return $this->addRecord(static::WARNING, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function addError($message, array $context = [])
    {
        return $this->addRecord(static::ERROR, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function addCritical($message, array $context = [])
    {
        return $this->addRecord(static::CRITICAL, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function addAlert($message, array $context = [])
    {
        return $this->addRecord(static::ALERT, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function addEmergency($message, array $context = [])
    {
        return $this->addRecord(static::EMERGENCY, $message, $context);
    }

    /**
     * @return array
     */
    public function getLoggedMessages()
    {
        return $this->messages;
    }
}
