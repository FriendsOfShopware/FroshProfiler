<?php

class Shopware_Controllers_Frontend_Profiler extends Enlight_Controller_Action {
    public function indexAction()
    {

    }

    public function phpAction()
    {
        phpinfo();
        die();
    }
}