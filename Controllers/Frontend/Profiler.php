<?php

class Shopware_Controllers_Frontend_Profiler extends Enlight_Controller_Action
{
    public function indexAction()
    {
        $this->View()->sIndex = Shopware()->Container()->get('shyim_profiler.cache')->fetch('index');
    }

    public function detailAction()
    {
        Shopware()->Container()->get('shyim_profiler.smarty_extensions')->addPlugins($this->View()->Engine());
        $detail = Shopware()->Container()->get('shyim_profiler.cache')->fetch($this->Request()->get('id'));

        if (empty($detail)) {
            $this->redirect([
                'action' => 'index'
            ]);
        }

        $this->View()->sId = $this->Request()->get('id');
        $this->View()->sDetail = $detail;
        $this->View()->sPanel = $this->Request()->getParam('panel', 'request');
    }

    public function phpAction()
    {
        phpinfo();
        die();
    }
}
