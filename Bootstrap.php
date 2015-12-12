<?php

class Shopware_Plugins_Core_Profiler_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    private $plugin_info = array(
        'version'     => "1.0.3",
        'label'       => "Profiler",
        'source'      => null,
        'changes'     => null,
        'license'     => null,
        'revision'    => null
    );

    private $plugin_capabilities = array(
        'install' => true,
        'update'  => true,
        'enable'  => true
    );

    private $invalidateCacheArray = array(
        "proxy",
        "frontend",
        "backend",
        "template",
        "config"
    );


    public function getVersion()
    {
        return "1.0.0";
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
            "Enlight_Controller_Front_DispatchLoopStartup",
            "onStartDispatch"
        );

        return array(
            'success'         => true,
            'invalidateCache' => $this->invalidateCacheArray
        );
    }


    public function onStartDispatch()
    {
        define('STARTTIME', microtime(true));

        $subscribers = array(
            new Shopware\Profiler\Subscriber\Collector($this),
            new \Shopware\Profiler\Subscriber\Service($this)
        );
        foreach( $subscribers as $subscriber )
            $this->Application()->Events()->addSubscriber( $subscriber );
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
}