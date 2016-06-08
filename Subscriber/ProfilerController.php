<?php
namespace ShyimProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\DependencyInjection\Container;

class ProfilerController implements SubscriberInterface
{
    /**
     * @var Container
     */
    private $container;

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_Profiler' => 'onProfilerController'
        ];
    }

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function onProfilerController()
    {
        $this->container->get('template')->addTemplateDir(
            $this->container->getParameter('shyim_profiler.plugin_dir') . '/Resources/views/'
        );

        return $this->container->getParameter('shyim_profiler.plugin_dir') . '/Controllers/Frontend/Profiler.php';
    }
}
