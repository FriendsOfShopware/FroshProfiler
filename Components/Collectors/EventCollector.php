<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use FroshProfiler\Components\Event\EventManager;
use FroshProfiler\Components\Struct\Profile;
use Symfony\Component\Stopwatch\StopwatchEvent;

class EventCollector implements CollectorInterface
{
    private $eventManager;

    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function getName(): string
    {
        return 'Events';
    }

    public function collect(Enlight_Controller_Action $controller, Profile $profile): void
    {
        $eventsTimes = [];

        /** @var StopwatchEvent $sectionEvent */
        foreach ($this->eventManager->getStopWatch()->getSectionEvents('__root__') as $eventName => $sectionEvent) {
            if ($sectionEvent->getStartTime() !== $sectionEvent->getEndTime()) {
                $eventsTimes[$eventName] = [
                    'name' => $eventName,
                    'start' => $sectionEvent->getStartTime(),
                    'end' => $sectionEvent->getEndTime(),
                    'duration' => $sectionEvent->getDuration(),
                    'listeners' => [],
                ];
            }
        }

        foreach ($eventsTimes as $key => $event) {
            list($eventName, $listener) = explode('|', $event['name']);

            if ($listener !== null) {
                $event['name'] = $listener;
                $eventsTimes[$eventName]['listeners'][] = $event;
                unset($eventsTimes[$key]);
            }
        }

        usort($eventsTimes, function ($a, $b) {
            return ($a['duration'] > $b['duration']) ? -1 : 1;
        });

        $chartLabels = [];
        $chartValues = [];
        $eventListeners = [];

        foreach ($eventsTimes as $eventsTime) {
            if ($eventsTime['duration'] === 0) {
                continue;
            }

            $chartLabels[] = $eventsTime['name'];
            $chartValues[] = $eventsTime['duration'];
            $eventListeners[$eventsTime['name']] = $eventsTime['listeners'];
        }

        $profile->setEvents([
            'eventAmount' => $this->eventManager->getEventAmount(),
            'calledEvents' => $this->eventManager->getCalledEvents(),
            'events' => $eventsTimes,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'eventListeners' => $eventListeners,
        ]);
    }

    public function getToolbarTemplate(): ?string
    {
        return '@Toolbar/toolbar/events.tpl';
    }
}
