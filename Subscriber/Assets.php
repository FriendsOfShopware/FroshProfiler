<?php
namespace Profiler\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;

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
                __DIR__ . '/Views/frontend/_public/src/less/all.less'
            ],
            __DIR__
        );

        return new ArrayCollection([$less]);
    }
}
