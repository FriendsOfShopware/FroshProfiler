<?php
namespace ShyimProfiler\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Theme\LessDefinition;

class Assets implements SubscriberInterface
{
    /**
     * @var string
     */
    private $viewDir;

    public static function getSubscribedEvents()
    {
        return [
            'Theme_Compiler_Collect_Plugin_Less' => 'addLessFiles'
        ];
    }

    /**
     * @param $viewDir
     */
    public function __construct($viewDir)
    {
        $this->viewDir = $viewDir;
    }

    public function addLessFiles()
    {
        $less = new LessDefinition(
            [],
            [
                $this->viewDir . '/frontend/_public/src/less/all.less'
            ],
            $this->viewDir . '/frontend/_public/src/less'
        );

        return new ArrayCollection([$less]);
    }
}
