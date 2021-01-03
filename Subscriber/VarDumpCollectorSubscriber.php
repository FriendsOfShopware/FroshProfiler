<?php

namespace FroshProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\VarDumper\VarDumper;

class VarDumpCollectorSubscriber implements SubscriberInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Bootstrap_AfterInitResource_front' => 'initVarDumper',
        ];
    }

    public function initVarDumper(): void
    {
        $container = $this->container;
        $pluginConfig = $container->get('frosh_profiler.config');

        if (!empty($pluginConfig['varDumpServer'])) {
            return;
        }


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
