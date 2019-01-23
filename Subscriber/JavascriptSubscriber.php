<?php

namespace FroshProfiler\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;

/**
 * Class JavascriptSubscriber
 */
class JavascriptSubscriber implements SubscriberInterface
{
    /**
     * @var array
     */
    private $pluginConfig;

    /**
     * @var string
     */
    private $viewDir;

    /**
     * JavascriptSubscriber constructor.
     *
     * @param array  $pluginConfig
     * @param string $viewDir
     */
    public function __construct(array $pluginConfig, $viewDir)
    {
        $this->pluginConfig = $pluginConfig;
        $this->viewDir = $viewDir;
    }

    /**
     * {@inheritdoc]
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Compiler_Collect_Plugin_Javascript' => 'onCollectJavascriptFiles',
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     *
     * @return ArrayCollection
     */
    public function onCollectJavascriptFiles(Enlight_Event_EventArgs $args)
    {
        $collection = new ArrayCollection();

        if ($this->pluginConfig['jsEvents']) {
            $collection->add($this->viewDir . '/frontend/profiler/_resources/js/js_events.js');
        }

        return $collection;
    }
}
