<?php

namespace ShyimProfiler\Components\Collectors;

use Doctrine\DBAL\Logging\DebugStack;
use Enlight_Components_Db_Adapter_Pdo_Mysql;
use Enlight_Controller_Action;
use Shopware\Components\Model\ModelManager;
use ShyimProfiler\Components\Cache\Cache;
use ShyimProfiler\Components\Struct\Profile;
use Zend_Db_Profiler;

/**
 * Class CacheCollector
 * @package ShyimProfiler\Components\Collectors
 */
class CacheCollector implements CollectorInterface
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * CacheCollector constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Cache';
    }

    /**
     * @param Enlight_Controller_Action $controller
     * @param Profile $profile
     */
    public function collect(Enlight_Controller_Action $controller, Profile $profile)
    {
        $profile->setAttributes([
            'cache' => [
                'calls' => $this->cache->calls,
                'read' => $this->cache->read,
                'write' => $this->cache->write,
                'delete' => $this->cache->delete,
                'hit' => $this->cache->hit,
                'hitMissed' => $this->cache->hitMissed,
                'time' => $this->cache->time,
                'backend' => get_class($this->cache->getBackend())
            ]
        ]);
    }

    /**
     * @return bool
     */
    public function getToolbarTemplate()
    {
        return '@Toolbar/toolbar/cache.tpl';
    }
}
