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

    public function __construct(array $pluginConfig, string $viewDir)
    {
        $this->pluginConfig = $pluginConfig;
        $this->viewDir = $viewDir;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Theme_Compiler_Collect_Plugin_Javascript' => 'onCollectJavascriptFiles',
        ];
    }

    public function onCollectJavascriptFiles(): ArrayCollection
    {
        $collection = new ArrayCollection();

        if ($this->pluginConfig['jsEvents']) {
            $collection->add($this->viewDir . '/frontend/profiler/_resources/js/js_events.js');
        }

        return $collection;
    }
}
