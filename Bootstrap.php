<?php

use Shopware\Components\Theme\LessDefinition;
use Shopware\Profiler\Subscriber\Collector;
use Shopware\Profiler\Subscriber\Decorator;
use Shopware\Profiler\Subscriber\Service;

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
        if(version_compare(Shopware::VERSION, '5', '<')) {
            throw new Exception("Only Shopware 5 upper is supported");
        }

        $this->subscribeEvent(
            'Enlight_Controller_Front_StartDispatch',
            'onStartDispatch',
            -500
        );

        $this->subscribeEvent(
            'Theme_Compiler_Collect_Plugin_Less',
            'addLessFiles'
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
            new Collector($this),
            new Service($this),
            new Decorator($this)
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

    public function addLessFiles()
    {
        $less = new LessDefinition(
            array(),
            array(
                __DIR__ . '/Views/responsive/frontend/_public/src/less/all.less'
            ),
            __DIR__
        );

        return new Doctrine\Common\Collections\ArrayCollection(array($less));
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
