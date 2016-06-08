<?php
namespace ShyimProfiler\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Theme\LessDefinition;

class Assets implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Compiler_Collect_Plugin_Less' => 'addLessFiles'
        ];
    }

    public function addLessFiles()
    {
        $less = new LessDefinition(
            [],
            [
                Shopware()->Container()->getParameter('shyim_profiler.plugin_dir') . '/Views/frontend/_public/src/less/all.less'
            ],
            __DIR__
        );

        return new ArrayCollection([$less]);
    }
}
