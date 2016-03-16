<?php

class Shopware_Controllers_Frontend_Profiler extends Enlight_Controller_Action
{
    public function indexAction()
    {
        $this->View()->sIndex = Shopware()->Container()->get('profiler.cache')->fetch('index');
    }

    public function detailAction()
    {
        $detail = Shopware()->Container()->get('profiler.cache')->fetch($this->Request()->get('id'));

        if(empty($detail)) {
            $this->redirect([
                'action' => 'index'
            ]);
        }

        $this->View()->sDetail = $detail;
    }

    public function phpAction()
    {
        phpinfo();
        die();
    }
}
