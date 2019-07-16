<?php

namespace FroshProfiler\Components\Collectors;

use Enlight_Controller_Action;
use FroshProfiler\Components\Cache\Cache;
use FroshProfiler\Components\Struct\Profile;

class CacheCollector implements CollectorInterface
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function getName(): string
    {
        return 'Cache';
    }

    public function collect(Enlight_Controller_Action $controller, Profile $profile): void
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
                'backend' => get_class($this->cache->getBackend()),
            ],
        ]);
    }

    public function getToolbarTemplate(): string
    {
        return '@Toolbar/toolbar/cache.tpl';
    }
}
