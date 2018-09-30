<?php

namespace FroshProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Template_Manager;
use FroshProfiler\Controller\Frontend\Profiler;

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

    /**
     * @param $viewDir
     * @param Enlight_Template_Manager $template
     */
    public function __construct(
        $viewDir,
        Enlight_Template_Manager $template
    ) {
        $this->viewDir = $viewDir;
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_Profiler' => 'onProfilerController',
        ];
    }

    /**
     * @return string
     */
    public function onProfilerController()
    {
        $this->template->addTemplateDir($this->viewDir);

        return Profiler::class;
    }
}
