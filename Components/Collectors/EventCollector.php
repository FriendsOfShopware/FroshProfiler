<?php
/**
 * Created by PhpStorm.
 * User: shyim
 * Date: 17.12.15
 * Time: 13:58.
 */

namespace ShyimProfiler\Components\Collectors;

use Enlight_Controller_Action;
use ShyimProfiler\Components\Event\EventManager;

class EventCollector implements CollectorInterface
{
    private $eventManager;

    /**
     * EventCollector constructor.
     *
     * @param EventManager $eventManager
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function getName()
    {
        return 'Events';
    }

    public function collect(Enlight_Controller_Action $controller)
    {
        return [
           'events' => [
               'eventAmount'  => $this->eventManager->getEventAmount(),
               'calledEvents' => $this->eventManager->getCalledEvents(),
           ],
        ];
    }

    public function getToolbarTemplate()
    {
        return '@Toolbar/toolbar/events.tpl';
    }
}
