<?php

namespace FroshProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Template_Manager;

/**
 * Class ProfilerController
 */
class ProfilerController implements SubscriberInterface
{
    /**
     * @var string
     */
    private $viewDir;

    /**
     * @var Enlight_Template_Manager
     */
    private $template;

    public function __construct(
        string $viewDir,
        Enlight_Template_Manager $template
    ) {
        $this->viewDir = $viewDir;
        $this->template = $template;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend_Profiler' => 'onProfilerController',
        ];
    }

    public function onProfilerController(): void
    {
        $this->template->addTemplateDir($this->viewDir);
    }
}
