<?php

namespace FroshProfiler\Components\Event;

use Closure;
use Doctrine\Common\Util\Debug;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Enlight_Event_Exception;
use Enlight_Event_Handler;
use Enlight_Event_Handler_Default;
use Enlight_Event_Handler_Plugin;
use Shopware\Components\ContainerAwareEventManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class EventManager
 */
class EventManager extends ContainerAwareEventManager
{
    /**
     * @var int
     */
    protected $eventsAmount = 0;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @var bool
     */
    private $xdebugInstalled = false;

    /**
     * @var int
     */
    private $xdebugDepth = 0;

    /**
     * @var Stopwatch
     */
    private $watch;

    /**
     * EventManager constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->xdebugInstalled = extension_loaded('xdebug');

        if ($this->xdebugInstalled) {
            $this->xdebugDepth = (int) ini_get('xdebug.var_display_max_depth');
        }
    }

    public function setStopWatch(Stopwatch $stopwatch)
    {
        $this->watch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    public function notify($event, $eventArgs = null)
    {
        $hasListeners = $this->hasListeners($event);

        $this->calledEvents[] = [
            'type' => 'notify',
            'name' => $event,
            'args' => $this->dump($eventArgs),
        ];

        if ($hasListeners) {
            $this->watch->start($event);
        }

        $response = $this->parentNotify($event, $eventArgs);

        if ($hasListeners) {
            $this->watch->stop($event);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($event, $value, $eventArgs = null)
    {
        $hasListeners = $this->hasListeners($event);

        if ($hasListeners) {
            $this->watch->start($event);
        }

        $afterValue = $this->parentFilter($event, $value, $eventArgs);

        if ($hasListeners) {
            $this->watch->stop($event);
        }

        $this->calledEvents[] = [
            'type' => 'filter',
            'name' => $event,
            'args' => $this->dump($eventArgs),
            'old' => is_object($value) ? $this->dump($value) : $value,
            'new' => is_object($afterValue) ? $this->dump($afterValue) : $afterValue,
        ];

        return $afterValue;
    }

    /**
     * {@inheritdoc}
     */
    public function notifyUntil($event, $eventArgs = null)
    {
        $hasListeners = $this->hasListeners($event);

        if ($hasListeners) {
            $this->watch->start($event);
        }

        $cancel = $this->parentNotifyUntil($event, $eventArgs);

        if ($hasListeners) {
            $this->watch->stop($event);
        }

        $this->calledEvents[] = [
            'type' => 'notifyUntil',
            'name' => $event,
            'args' => $this->dump($eventArgs),
            'cancel' => is_object($cancel) ? $this->dump($cancel) : $cancel,
        ];

        return $cancel;
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, [$subscriber, $params]);
            } elseif (is_string($params[0])) {
                $this->addListener($eventName, [$subscriber, $params[0]], isset($params[1]) ? $params[1] : 0);
            } else {
                foreach ($params as $listener) {
                    $this->addListener($eventName, [$subscriber, $listener[0]], isset($listener[1]) ? $listener[1] : 0);
                }
            }
        }
    }

    /**
     * @param string $eventName
     * @param array|callable $listener
     * @param int $priority
     * @return \Enlight_Event_EventManager
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        ++$this->eventsAmount;

        return parent::addListener($eventName, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function registerListener(\Enlight_Event_Handler $handler)
    {
        ++$this->eventsAmount;

        return parent::registerListener($handler);
    }

    /**
     * {@inheritdoc}
     */
    public function registerSubscriber(\Enlight_Event_Subscriber $subscriber)
    {
        $listeners = $subscriber->getListeners();

        foreach ($listeners as $listener) {
            $this->registerListener($listener);
        }
    }

    /**
     * @return int
     */
    public function getEventAmount()
    {
        return $this->eventsAmount;
    }

    /**
     * @return array
     */
    public function getCalledEvents()
    {
        return $this->calledEvents;
    }

    /**
     * @return Stopwatch
     */
    public function getStopWatch()
    {
        return $this->watch;
    }

    /**
     * @param string $event
     * @param mixed $value
     * @param Enlight_Event_EventArgs|null $eventArgs
     *
     * @return mixed
     */
    public function parentFilter($event, $value, $eventArgs = null)
    {
        if (!$this->hasListeners($event)) {
            return $value;
        }

        $eventArgs = $this->buildEventArgs($eventArgs);
        $eventArgs->setReturn($value);
        $eventArgs->setName($event);
        $eventArgs->setProcessed(false);

        /** @var Enlight_Event_Handler_Default $listener */
        foreach ($this->getListeners($event) as $listener) {
            $eventName = $this->getEventName($event, $listener);
            if ($eventName !== null) {
                $this->watch->start($eventName);
            }
            if (($return = $listener->execute($eventArgs)) !== null) {
                $eventArgs->setReturn($return);
            }
            if ($eventName !== null) {
                $this->watch->stop($eventName);
            }
        }
        $eventArgs->setProcessed(true);

        return $eventArgs->getReturn();
    }

    /**
     * @param mixed $argument
     *
     * @return string
     */
    private function dump($argument): string
    {
        $value = Debug::dump($argument, 2, true, false);
        if ($this->xdebugInstalled) {
            ini_set('xdebug.var_display_max_depth', $this->xdebugDepth);
        }

        return $value;
    }

    /**
     * @param string $event
     * @param Enlight_Event_EventArgs|null $eventArgs
     *
     * @return Enlight_Event_EventArgs|null
     */
    private function parentNotify($event, $eventArgs = null)
    {
        if (!$this->hasListeners($event)) {
            return null;
        }

        $eventArgs = $this->buildEventArgs($eventArgs);
        $eventArgs->setReturn(null);
        $eventArgs->setName($event);
        $eventArgs->setProcessed(false);

        /** @var Enlight_Event_Handler_Plugin $listener */
        foreach ($this->getListeners($event) as $listener) {
            $eventName = $this->getEventName($event, $listener);
            if ($eventName !== null) {
                $this->watch->start($eventName);
            }
            $listener->execute($eventArgs);
            if ($eventName !== null) {
                $this->watch->stop($eventName);
            }
        }
        $eventArgs->setProcessed(true);

        return $eventArgs;
    }

    /**
     * @param mixed $eventArgs
     */
    private function parentNotifyUntil(string $event, $eventArgs = null): ?Enlight_Event_EventArgs
    {
        if (!$this->hasListeners($event)) {
            return null;
        }

        $eventArgs = $this->buildEventArgs($eventArgs);
        $eventArgs->setReturn(null);
        $eventArgs->setName($event);
        $eventArgs->setProcessed(false);

        /** @var Enlight_Event_Handler_Default $listener */
        foreach ($this->getListeners($event) as $listener) {
            $eventName = $this->getEventName($event, $listener);
            if ($eventName !== null) {
                $this->watch->start($eventName);
            }
            if (($return = $listener->execute($eventArgs)) !== null
                || $eventArgs->isProcessed()
            ) {
                $eventArgs->setProcessed(true);
                $eventArgs->setReturn($return);
            }
            if ($eventName !== null) {
                $this->watch->stop($eventName);
            }
            if ($eventArgs->isProcessed()) {
                return $eventArgs;
            }
        }

        return null;
    }

    /**
     * @param null $eventArgs
     *
     * @throws Enlight_Event_Exception
     *
     * @return Enlight_Event_EventArgs|null
     */
    private function buildEventArgs($eventArgs = null)
    {
        if (isset($eventArgs) && is_array($eventArgs)) {
            return new Enlight_Event_EventArgs($eventArgs);
        } elseif (!isset($eventArgs)) {
            return new Enlight_Event_EventArgs();
        } elseif (!$eventArgs instanceof Enlight_Event_EventArgs) {
            throw new Enlight_Event_Exception('Parameter "eventArgs" must be an instance of "Enlight_Event_EventArgs"');
        }

        return $eventArgs;
    }

    /**
     * @param string                $event
     * @param Enlight_Event_Handler $listener
     *
     * @return string
     */
    private function getEventName($event, Enlight_Event_Handler $listener)
    {
        $eventName = null;
        $lis = $listener->getListener();

        if ($listener instanceof Enlight_Event_Handler_Default) {
            if ($lis instanceof Closure) {
                $eventName = $event . '|Closure::Closure';
            } elseif (is_array($lis)) {
                /** @var object $classObj */
                $classObj = $lis[0];
                /** @var string $classMethod */
                $classMethod = $lis[1];

                $eventName = $event . '|' . get_class($classObj) . '::' . $classMethod;
            }
        } elseif ($listener instanceof Enlight_Event_Handler_Plugin) {
            $eventName = $event . '|' . get_class($listener->Plugin()) . '::' . $lis;
        }

        return $eventName;
    }
}
