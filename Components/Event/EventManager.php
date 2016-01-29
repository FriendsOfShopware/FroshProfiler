<?php

namespace Shopware\Profiler\Components\Event;

class EventManager extends \Enlight_Event_EventManager
{
    protected $eventsAmount = 0;
    protected $calledEvents = [];

    /**
     * EventManager constructor.
     *
     * @param \Enlight_Event_EventManager $eventManager
     */
    public function __construct($eventManager)
    {
        $this->listeners = $eventManager->getAllListeners();
        $this->eventsAmount = count($this->listeners);
        parent::__construct();
    }

    public function notify($event, $eventArgs = null)
    {
        $this->calledEvents[] = [
            $event,
            $eventArgs,
        ];

        return parent::notify($event, $eventArgs);
    }

    public function notifyUntil($event, $eventArgs = null)
    {
        $this->calledEvents[] = [
            $event,
            $eventArgs,
        ];

        return parent::notifyUntil($event, $eventArgs);
    }

    public function filter($event, $value, $eventArgs = null)
    {
        $this->calledEvents[] = [
            $event,
            $eventArgs,
        ];

        return parent::filter($event, $value, $eventArgs);
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->eventsAmount++;

        return parent::addListener($eventName, $listener, $priority);
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
