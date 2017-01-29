<?php

namespace ShyimProfiler\Components;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use Enlight_Event_EventManager;
use Monolog\Formatter\NormalizerFormatter;
use Shopware\Components\Plugin\CachedConfigReader;
use ShyimProfiler\Components\Collectors\CollectorInterface;

class Collector
{
    /**
     * @var CollectorInterface[]
     */
    private $collectors = [];

    /**
     * @var Enlight_Event_EventManager
     */
    private $events;

    /**
     * @var CacheProvider
     */
    private $cache;

    /**
     * @var NormalizerFormatter
     */
    private $normalizer;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $pluginConfig;

    public function __construct(
        Enlight_Event_EventManager $events,
        CacheProvider $cache,
        CachedConfigReader $configReader,
        Connection $connection
    ) {
        $this->events = $events;
        $this->cache = $cache;
        $this->normalizer = new NormalizerFormatter();
        $this->pluginConfig = $configReader->getByPluginName('ShyimProfiler');
        $this->connection = $connection;
    }

    /**
     * @param CollectorInterface $collector
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function addCollector(CollectorInterface $collector)
    {
        $this->collectors[] = $collector;
    }

    /**
     * @return CollectorInterface[]
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    public function collectInformation(\Enlight_Controller_Action $controller)
    {
        $result = [];

        foreach ($this->collectors as $collector) {
            if ($collector instanceof CollectorInterface) {
                $result = array_merge($result, $collector->collect($controller));
            }
        }

        return $result;
    }

    public function saveCollectInformation($id, $information, $subrequets = false)
    {
        $information = $this->normalizer->format($information);

        if ($subrequets) {
            $data = $this->cache->fetch($id);
            $data['subrequest'][] = $information;
            $this->cache->save($id, $data);
        } else {
            $this->cache->save($id, $information);

            $this->connection->insert('s_plugin_profiler', [
                'token'  => $id,
                'method' => $information['request']['httpMethod'],
                'status' => $information['response']['httpResponse'],
                'ip'     => $information['request']['ip'],
                'url'    => $information['request']['uri'],
                'time'   => date('Y-m-d H:i:s')
            ]);

            $profileCount = $this->connection->fetchColumn('SELECT COUNT(*) FROM s_plugin_profiler');

            if ($profileCount > $this->pluginConfig['maxProfiles'] && !empty($this->pluginConfig['maxProfiles'])) {
                $deleteProfiles = $profileCount - $this->pluginConfig['maxProfiles'];

                $deleteProfileQuery = $this->connection->createQueryBuilder()
                    ->from('s_plugin_profiler')
                    ->orderBy('time ASC')
                    ->setMaxResults($deleteProfiles)
                    ->addSelect('token')
                    ->execute()
                    ->fetchAll(\PDO::FETCH_COLUMN);

                foreach ($deleteProfileQuery as $token) {
                    $this->cache->delete($token);
                }

                $this->connection->executeQuery('DELETE FROM s_plugin_profiler WHERE token IN(?)', [$deleteProfileQuery], [Connection::PARAM_STR_ARRAY]);
            }
        }

        return $id;
    }
}
