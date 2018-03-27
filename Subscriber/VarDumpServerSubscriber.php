<?php

namespace FroshProfiler\Subscriber;


use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class VarDumpServerSubscriber
 * @author Soner Sayakci <shyim@posteo.de>
 */
class VarDumpServerSubscriber implements SubscriberInterface
{
    /**
     * @var array
     */
    private $pluginConfig;

    /**
     * @var ServerDumper
     */
    private $serverDumper;

    /**
     * VarDumpServerSubscriber constructor.
     * @param array $pluginConfig
     * @param ServerDumper $serverDumper
     * @author Soner Sayakci <shyim@posteo.de>
     */
    public function __construct(array $pluginConfig, ServerDumper $serverDumper)
    {
        $this->pluginConfig = $pluginConfig;
        $this->serverDumper = $serverDumper;
    }

    /**
     * @return array
     * @author Soner Sayakci <shyim@posteo.de>
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Bootstrap_AfterInitResource_models' => 'initVarDumper',
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     * @author Soner Sayakci <shyim@posteo.de>
     */
    public function initVarDumper(Enlight_Event_EventArgs $args)
    {
        if (empty($this->pluginConfig['varDumpServer'])) {
            return;
        }

        $cloner = new VarCloner();
        $cloner->setMaxItems(-1);
        $serverDumper = $this->serverDumper;

        VarDumper::setHandler(function($var) use ($cloner, $serverDumper) {
            $data = $cloner->cloneVar($var);
            $serverDumper->dump($data);
        });
    }
}