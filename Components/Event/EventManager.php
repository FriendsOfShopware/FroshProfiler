<?php

namespace Shopware\Profiler\Components\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Enlight\Event\SubscriberInterface;

class EventManager extends \Enlight_Event_EventManager
{
    protected $eventsAmount = 0;
    protected $calledEvents = [];
    protected $parentEventManager;

    /**
     * @param $events \Enlight_Event_EventManager
     */
    public function __construct(\Enlight_Event_EventManager $events)
    {
        $this->listeners = $events->getAllListeners();
        $this->eventsAmount = count($this->listeners);
        $this->parentEventManager = $events;
    }

    /**
     * @param string $event
     * @param null $eventArgs
     * @return \Enlight_Event_EventArgs|null
     * @throws \Enlight_Event_Exception
     */
    public function notify($event, $eventArgs = null)
    {
        $this->calledEvents[] = [
            'type' => 'notify',
            'name' => $event,
            'args' => Debug::dump($eventArgs, 2, true, false)
        ];

        return $this->parentEventManager->notify($event, $eventArgs);
    }

    /**
     * @param string $event
     * @param mixed $value
     * @param null $eventArgs
     * @return mixed
     * @throws \Enlight_Event_Exception
     */
    public function filter($event, $value, $eventArgs = null)
    {
        $afterValue = $this->parentEventManager->filter($event, $value, $eventArgs);

        $this->calledEvents[] = [
            'type' => 'filter',
            'name' => $event,
            'args' => Debug::dump($eventArgs, 2, true, false),
            'old' => (is_object($value) ? Debug::dump($value, 2, true, false) : $value),
            'new' => (is_object($afterValue) ? Debug::dump($afterValue, 2, true, false) : $afterValue)
        ];

        return $afterValue;
    }

    /**
     * @param string $event
     * @param null $eventArgs
     * @return \Enlight_Event_EventArgs|null
     * @throws \Enlight_Exception
     */
    public function notifyUntil($event, $eventArgs = null)
    {
        $cancel = $this->parentEventManager->notifyUntil($event, $eventArgs);
        $this->calledEvents[] = [
            'type' => 'notifyUntil',
            'name' => $event,
            'args' => Debug::dump($eventArgs, 2, true, false),
            'cancel' => (is_object($cancel) ? Debug::dump($cancel, 2, true, false) : $cancel)
        ];

        return $cancel;
    }

    /**
     * @param $event
     * @return \Enlight_Event_Handler[]
     */
    public function getListeners($event)
    {
        return $this->parentEventManager->getListeners($event);
    }

    /**
     * @param SubscriberInterface $subscriber
     */
    public function addSubscriber(SubscriberInterface $subscriber)
    {
        return $this->parentEventManager->addSubscriber($subscriber);
    }

    /**
     * @param $eventName
     * @param $listener
     * @param int $priority
     * @return \Enlight_Event_EventManager
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->eventsAmount++;
        return $this->parentEventManager->addListener($eventName, $listener, $priority);
    }

    /**
     * @param $event
     * @param ArrayCollection $collection
     * @param null $eventArgs
     * @return ArrayCollection|null
     * @throws \Enlight_Event_Exception
     */
    public function collect($event, ArrayCollection $collection, $eventArgs = null)
    {
        return $this->parentEventManager->collect($event, $collection, $eventArgs);
    }

    /**
     * @return array
     */
    public function getAllListeners()
    {
        return $this->parentEventManager->getAllListeners();
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->parentEventManager->getEvents();
    }

    /**
     * @param string $event
     * @return bool
     */
    public function hasListeners($event)
    {
        return $this->parentEventManager->hasListeners($event);
    }

    /**
     * @param \Enlight_Event_Handler $handler
     * @return \Enlight_Event_EventManager
     */
    public function registerListener(\Enlight_Event_Handler $handler)
    {
        return $this->parentEventManager->registerListener($handler);
    }

    /**
     * @param \Enlight_Event_Subscriber $subscriber
     */
    public function registerSubscriber(\Enlight_Event_Subscriber $subscriber)
    {
        $this->parentEventManager->registerSubscriber($subscriber);
    }

    /**
     * @param \Enlight_Event_Handler $handler
     * @return \Enlight_Event_EventManager
     */
    public function removeListener(\Enlight_Event_Handler $handler)
    {
        return $this->parentEventManager->removeListener($handler);
    }

    /**
     * @return \Enlight_Event_EventManager
     */
    public function reset()
    {
        return $this->parentEventManager->reset();
    }

    public function getEventAmount()
    {
        return $this->eventsAmount;
    }

    public function getCalledEvents()
    {
        return $this->calledEvents;
    }
}
