<?php

class Shopware_Plugins_Core_Profiler_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    private $plugin_info = [
        'version'     => '1.0.3',
        'label'       => 'Profiler',
        'source'      => null,
        'changes'     => null,
        'license'     => null,
        'revision'    => null,
    ];

    private $plugin_capabilities = [
        'install' => true,
        'update'  => true,
        'enable'  => true,
    ];

    private $invalidateCacheArray = [
        'proxy',
        'frontend',
        'backend',
        'template',
        'config',
    ];

    public function getVersion()
    {
        return '1.0.0';
    }

    public function getLabel()
    {
        return $this->plugin_info['label'];
    }

    public function getInfo()
    {
        return $this->plugin_info;
    }

    public function getCapabilities()
    {
        return $this->plugin_capabilities;
    }

    public function install()
    {
        $this->subscribeEvent(
            'Enlight_Controller_Front_StartDispatch',
            'onStartDispatch'
        );

        $this->registerController('Frontend', 'Profiler');

        return [
            'success'         => true,
            'invalidateCache' => $this->invalidateCacheArray,
        ];
    }

    public function onStartDispatch()
    {
        define('STARTTIME', microtime(true));

        $subscribers = [
            new Shopware\Profiler\Subscriber\Collector($this),
            new \Shopware\Profiler\Subscriber\Service($this),
        ];

        foreach ($subscribers as $subscriber) {
            $this->Application()->Events()->addSubscriber($subscriber);
        }

        $this->initCustomEventManager();
        $this->initDatabaseProfiler();
    }

    public function afterInit()
    {
        $this->Application()->Loader()->registerNamespace(
            'Shopware\Profiler',
            $this->Path()
        );
    }

    public function uninstall()
    {
        return true;
    }

    private function initCustomEventManager()
    {
        $event = new \Shopware\Profiler\Components\Event\EventManager($this->Application()->Events());
        Shopware()->Container()->set('profiler.event_manager', $event);
        Shopware()->setEventManager($event);
    }

    private function initDatabaseProfiler()
    {
        // Zend DB Profiler
        Shopware()->Db()->setProfiler(new Zend_Db_Profiler(true));

        // Doctrine Profiler
        $logger = new \Doctrine\DBAL\Logging\DebugStack();
        $logger->enabled = true;
        Shopware()->Models()->getConfiguration()->setSQLLogger($logger);
    }
}
