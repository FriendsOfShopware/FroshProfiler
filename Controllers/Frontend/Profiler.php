<?php

use Doctrine\Common\Cache\FilesystemCache;

class Shopware_Controllers_Frontend_Profiler extends Enlight_Controller_Action
{
    /**
     * @var FilesystemCache
     */
    private $cache;

    public function preDispatch()
    {
        $this->cache = $this->get('shyim_profiler.cache');
        $this->get('shyim_profiler.smarty_extensions')->addPlugins($this->View()->Engine());
    }

    public function indexAction()
    {
        $index = $this->cache->fetch('index');

        $this->View()->sIndex = empty($index) ? [] : $index;
    }

    public function detailAction()
    {
        $id = $this->Request()->get('id');
        $subrequest = null;

        if (strpos($id, '|') !== false) {
            list($id, $subrequest) = explode('|', $id);
        }

        $detail = $this->cache->fetch($id);

        if ($subrequest != null) {
            $detail = $detail['subrequest'][$subrequest];
        }

        if (empty($detail)) {
            $this->redirect([
                'action' => 'index',
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

    public function mailAction()
    {
        $mode = $this->Request()->getParam('mode', 'bodyHtml');
        $detail = $this->cache->fetch($this->Request()->get('id'));
        $mail = $detail['mails'][$this->Request()->getParam('mailId')];

        $this->View()->mode = $mode;
        if ($mode instanceof Zend_Mime_Part) {
            $this->View()->data = $mail[$mode]->getContent();
        } else {
            $this->View()->data = $mail[$mode];
        }
    }
}
