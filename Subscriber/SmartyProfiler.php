<?php

namespace FroshProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;

/**
 * Class SmartyProfiler
 */
class SmartyProfiler implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDir;

    public function __construct(string $pluginDir)
    {
        $this->pluginDir = $pluginDir;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Bootstrap_InitResource_template' => 'onInitTemplate',
        ];
    }

    public function onInitTemplate(): void
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            if (strpos($_SERVER['REQUEST_URI'], '/backend') === false && strpos($_SERVER['REQUEST_URI'], '/api') === false && strpos($_SERVER['REQUEST_URI'], 'Profiler') === false) {
                /*
                 * Set a custom SYSPLUGINS Path, to disable default smarty autoloading
                 */
                define('SMARTY_SYSPLUGINS_DIR', $this->pluginDir);
            }
        }
    }
}
