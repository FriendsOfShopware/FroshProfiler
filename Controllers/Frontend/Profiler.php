<?php

class Shopware_Controllers_Frontend_Profiler extends Enlight_Controller_Action
{
    public function indexAction()
    {
        $this->View()->sIndex = Shopware()->Container()->get('profiler.cache')->fetch('index');
    }

    public function detailAction()
    {

    }

    public function phpAction()
    {
        phpinfo();
        die();
    }
}
