<?php

namespace FroshProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\VarDumper\VarDumper;

class VarDumpCollectorSubscriber implements SubscriberInterface
{
    /** @var array */
    private $pluginConfig;

    /** @var ContainerInterface */
    private $container;

    public function __construct(array $pluginConfig, ContainerInterface $container)
    {
        $this->pluginConfig = $pluginConfig;
        $this->container = $container;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Bootstrap_AfterInitResource_models' => 'initVarDumper',
        ];
    }

    public function initVarDumper(): void
    {
        if (!empty($this->pluginConfig['varDumpServer'])) {
            return;
        }

        $container = $this->container;

        // This code is here to lazy load the dump stack. This default
        // configuration is overridden in CLI mode on 'console.command' event.
        // The dump data collector is used by default, so dump output is sent to
        // the WDT. In a CLI context, if dump is used too soon, the data collector
        // will buffer it, and release it at the end of the script.
        VarDumper::setHandler(function ($var) use ($container) {
            $dumper = $container->get('frosh_profiler.collectors.dump');
            $cloner = $container->get('var_dumper.cloner');
            $handler = function ($var) use ($dumper, $cloner) {
                $dumper->dump($cloner->cloneVar($var));
            };
            VarDumper::setHandler($handler);
            $handler($var);
        });
    }
}
