<?php
namespace ShyimProfiler\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Theme\LessDefinition;
use Shopware\Components\DependencyInjection\Container;

class Assets implements SubscriberInterface
{
    /**
     * @var Container
     */
    private $container;

    public static function getSubscribedEvents()
    {
        return [
            'Theme_Compiler_Collect_Plugin_Less' => 'addLessFiles'
        ];
    }

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function addLessFiles()
    {
        $less = new LessDefinition(
            [],
            [
                $this->container->getParameter('shyim_profiler.plugin_dir') . '/Resources/views/frontend/_public/src/less/all.less'
            ],
            __DIR__
        );

        return new ArrayCollection([$less]);
    }
}
