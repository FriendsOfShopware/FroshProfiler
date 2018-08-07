<?php

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class Shopware_Controllers_Frontend_Profiler
 */
class Shopware_Controllers_Frontend_Profiler extends Enlight_Controller_Action
{
    /**
     * @var FilesystemCache
     */
    private $cache;

    /**
     * {@inheritdoc}
     *
     * @throws \Enlight_Controller_Exception
     */
    public function preDispatch()
    {
        $this->cache = $this->get('frosh_profiler.cache');
        $config = $this->get('frosh_profiler.config');

        if (!empty(trim($config['whitelistIP']))) {
            $isIPWhitelisted = in_array($this->get('front')->Request()->getClientIp(), explode("\n", $config['whitelistIP']), false);
            if (!$isIPWhitelisted) {
                throw new Enlight_Controller_Exception(
                    'Controller "' . $this->Request()->getControllerName() . '" not found',
                    Enlight_Controller_Exception::Controller_Dispatcher_Controller_Not_Found
                );
            }
        }
    }

    /**
     * @throws Exception
     */
    public function indexAction()
    {
        $query = $this->buildListQuery();
        $this->View()->params = $this->Request()->getParams();

        $this->View()->sIndex = $query->execute()->fetchAll();

        // "Latest button"
        if ($this->Request()->getParam('limit') == 1) {
            $index = current($this->View()->sIndex);

            if (!empty($index)) {
                $this->redirect([
                    'action' => 'detail',
                    'id' => $index['token'],
                ]);
            }
        }
    }

    /**
     * @throws Exception
     */
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

            return;
        }

        $eventFilter = [
            'showContainerEvents' => $this->Request()->getParam('showContainerEvents', 0),
            'search' => $this->Request()->getParam('search'),
        ];
        $detail = $this->filterDetail($detail, $eventFilter);

        $this->View()->eventFilter = $eventFilter;
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

    public function ajaxAction()
    {
        $this->Front()->Plugins()->Json()->setRenderer();
        $this->View()->setTemplate();
        $data = $this->cache->fetch($this->Request()->getParam('hash'));
        $this->View()->assign($data);
    }

    /**
     * @throws Exception
     *
     * @return QueryBuilder
     */
    private function buildListQuery()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->get('dbal_connection')->createQueryBuilder();

        $query = $queryBuilder
            ->select('*')
            ->from('s_plugin_profiler')
            ->setMaxResults($this->Request()->getParam('limit', 10))
            ->orderBy('time', 'DESC');

        if ($this->Request()->getParam('method')) {
            $query
                ->andWhere('method = :method')
                ->setParameter('method', $this->Request()->getParam('method'));
        }

        if ($this->Request()->getParam('token')) {
            $query
                ->andWhere('token = :token')
                ->setParameter('token', $this->Request()->getParam('token'));
        }

        if ($this->Request()->getParam('ip')) {
            $query
                ->andWhere('ip LIKE :ip')
                ->setParameter('ip', '%' . $this->Request()->getParam('ip') . '%');
        }

        if ($this->Request()->getParam('url')) {
            $query
                ->andWhere('url LIKE :url')
                ->setParameter('url', '%' . $this->Request()->getParam('url') . '%');
        }

        if ($this->Request()->getParam('start')) {
            $query
                ->andWhere('time >= :start')
                ->setParameter('start', $this->Request()->getParam('start'));
        }

        if ($this->Request()->getParam('end')) {
            $query
                ->andWhere('time <= :end')
                ->setParameter('end', $this->Request()->getParam('end'));
        }

        return $query;
    }

    /**
     * @param array $detail
     * @param array $eventFilter
     *
     * @return array
     */
    private function filterDetail(array $detail, array $eventFilter)
    {
        if ($this->Request()->getParam('panel') === 'events') {
            $detail['events']['calledEvents'] = \array_filter($detail['events']['calledEvents'], function ($value) use ($eventFilter) {
                if (!$eventFilter['showContainerEvents'] && \strpos($value['name'], 'Enlight_Bootstrap') === 0) {
                    return false;
                }

                if ($eventFilter['search'] && \stripos($value['name'], $eventFilter['search']) === false) {
                    return false;
                }

                return true;
            });
        }

        return $detail;
    }
}
