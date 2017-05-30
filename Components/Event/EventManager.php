<?php

namespace ShyimProfiler\Components\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\ContainerAwareEventManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EventManager
 * @package ShyimProfiler\Components\Event
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
     * EventManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->xdebugInstalled = extension_loaded('xdebug');

        if ($this->xdebugInstalled) {
            $this->xdebugDepth = ini_get('xdebug.var_display_max_depth');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function notify($event, $eventArgs = null)
    {
        $this->calledEvents[] = [
            'type' => 'notify',
            'name' => $event,
            'args' => $this->dump($eventArgs)
        ];

        return parent::notify($event, $eventArgs);
    }

    /**
     * {@inheritdoc}
     */
    public function filter($event, $value, $eventArgs = null)
    {
        $afterValue = parent::filter($event, $value, $eventArgs);

        $this->calledEvents[] = [
            'type' => 'filter',
            'name' => $event,
            'args' => $this->dump($eventArgs),
            'old'  => is_object($value) ? $this->dump($value) : $value,
            'new'  => is_object($afterValue) ? $this->dump($afterValue) : $afterValue,
        ];

        return $afterValue;
    }

    /**
     * {@inheritdoc}
     */
    public function notifyUntil($event, $eventArgs = null)
    {
        $cancel = parent::notifyUntil($event, $eventArgs);
        $this->calledEvents[] = [
            'type'   => 'notifyUntil',
            'name'   => $event,
            'args'   => $this->dump($eventArgs),
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
     * {@inheritdoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        ++$this->eventsAmount;

        return parent::addListener($eventName, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function collect($event, ArrayCollection $collection, $eventArgs = null)
    {
        return parent::collect($event, $collection, $eventArgs);
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
     * @param $argument
     * @return string
     */
    private function dump($argument)
    {
        $value = Debug::dump($argument, 2, true, false);
        if ($this->xdebugInstalled) {
            ini_set('xdebug.var_display_max_depth', $this->xdebugDepth);
        }

        return $value;
    }
}
