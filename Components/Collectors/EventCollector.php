<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use ShyimProfiler\Components\Event\EventManager;
use ShyimProfiler\Components\Struct\Profile;

/**
 * Class EventCollector
 * @package ShyimProfiler\Components\Collectors
 */
class EventCollector implements CollectorInterface
{
    private $eventManager;

    /**
     * EventCollector constructor.
     *
     * @param EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Events';
    }

    /**
     * @param Enlight_Controller_Action $controller
     * @param Profile $profile
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
    {
        $profile->setEvents([
            'eventAmount'  => $this->eventManager->getEventAmount(),
            'calledEvents' => $this->eventManager->getCalledEvents(),
        ]);
    }

    /**
     * @return string
     */
    public function getToolbarTemplate()
    {
        return '@Toolbar/toolbar/events.tpl';
    }
}
