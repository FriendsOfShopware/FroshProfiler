<?php

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use ShyimProfiler\Components\Event\EventManager;
use ShyimProfiler\Components\Struct\Profile;
use Symfony\Component\Stopwatch\StopwatchEvent;

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
        $eventsTimes = [];

        /** @var StopwatchEvent $sectionEvent */
        foreach ($this->eventManager->getStopWatch()->getSectionEvents('__root__') as $eventName => $sectionEvent) {
            if ($sectionEvent->getStartTime() !== $sectionEvent->getEndTime()) {
                $eventsTimes[] = [
                    'name'     => $eventName,
                    'start'    => $sectionEvent->getStartTime(),
                    'end'      => $sectionEvent->getEndTime(),
                    'duration' => $sectionEvent->getDuration()
                ];
            }
        }

        usort($eventsTimes, function($a, $b) {
            return ($a['duration'] > $b['duration']) ? -1 : 1;
        });

        $chartLabels = [];
        $chartValues = [];

        foreach ($eventsTimes as $eventsTime) {
            $chartLabels[] = $eventsTime['name'];
            $chartValues[] = $eventsTime['duration'];
        }

        $profile->setEvents([
            'eventAmount'  => $this->eventManager->getEventAmount(),
            'calledEvents' => $this->eventManager->getCalledEvents(),
            'events'       => $eventsTimes,
            'chartLabels'  => $chartLabels,
            'chartValues'  => $chartValues
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
