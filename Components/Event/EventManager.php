<?php

namespace ShyimProfiler\Components\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\ContainerAwareEventManager;

class EventManager extends ContainerAwareEventManager
{
    protected $eventsAmount = 0;
    protected $calledEvents = [];

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

        return parent::notify($event, $eventArgs);
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
        $afterValue = parent::filter($event, $value, $eventArgs);

        $this->calledEvents[] = [
            'type' => 'filter',
            'name' => $event,
            'args' => Debug::dump($eventArgs, 2, true, false),
            'old' => is_object($value) ? Debug::dump($value, 2, true, false) : $value,
            'new' => is_object($afterValue) ? Debug::dump($afterValue, 2, true, false) : $afterValue
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
        $cancel = parent::notifyUntil($event, $eventArgs);
        $this->calledEvents[] = [
            'type' => 'notifyUntil',
            'name' => $event,
            'args' => Debug::dump($eventArgs, 2, true, false),
            'cancel' => is_object($cancel) ? Debug::dump($cancel, 2, true, false) : $cancel
        ];

        return $cancel;
    }

    /**
     * @param SubscriberInterface $subscriber
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
     * @param $eventName
     * @param $listener
     * @param int $priority
     * @return \Enlight_Event_EventManager
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->eventsAmount++;
        return parent::addListener($eventName, $listener, $priority);
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
        return parent::collect($event, $collection, $eventArgs);
    }

    /**
     * @param \Enlight_Event_Handler $handler
     * @return \Enlight_Event_EventManager
     */
    public function registerListener(\Enlight_Event_Handler $handler)
    {
        $this->eventsAmount++;
        return parent::registerListener($handler);
    }

    /**
     * @param \Enlight_Event_Subscriber $subscriber
     */
    public function registerSubscriber(\Enlight_Event_Subscriber $subscriber)
    {
        $listeners = $subscriber->getListeners();

        foreach ($listeners as $listener) {
            $this->registerListener($listener);
        }
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
