<?php

namespace FroshProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class VarDumpServerSubscriber
 *
 * @author Soner Sayakci <shyim@posteo.de>
 */
class VarDumpServerSubscriber implements SubscriberInterface
{
    /**
     * @var array
     */
    private $pluginConfig;

    public function __construct(array $pluginConfig)
    {
        $this->pluginConfig = $pluginConfig;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Bootstrap_AfterInitResource_models' => 'initVarDumper',
        ];
    }

    public function initVarDumper(Enlight_Event_EventArgs $args): void
    {
        if (empty($this->pluginConfig['varDumpServer'])) {
            return;
        }

        $cloner = new VarCloner();
        $cloner->setMaxItems(-1);

        /** @var ServerDumper $serverDumper */
        $serverDumper = $args->get('subject')->get('var_dumper.server_dumper');

        VarDumper::setHandler(function ($var) use ($cloner, $serverDumper) {
            $data = $cloner->cloneVar($var);
            $serverDumper->dump($data);
        });
    }
}
