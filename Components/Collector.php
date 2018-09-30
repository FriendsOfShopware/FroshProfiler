<?php

namespace FroshProfiler\Components;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use Enlight_Controller_Action;
use FroshProfiler\Components\Collectors\CollectorInterface;
use FroshProfiler\Components\Struct\Profile;
use IteratorAggregate;

/**
 * Class Collector
 */
class Collector
{
    /**
     * @var CollectorInterface[]
     */
    private $collectors = [];

    /**
     * @var CacheProvider
     */
    private $cache;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $pluginConfig;

    /**
     * @var Profile
     */
    private $profile;

    /**
     * Collector constructor.
     *
     * @param IteratorAggregate $collectors
     * @param CacheProvider $cache
     * @param Connection $connection
     * @param Profile $profile
     * @param array $pluginConfig
     */
    public function __construct(
        IteratorAggregate $collectors,
        CacheProvider $cache,
        Connection $connection,
        Profile $profile,
        array $pluginConfig
    ) {
        $this->collectors = iterator_to_array($collectors, false);
        $this->cache = $cache;
        $this->pluginConfig = $pluginConfig;
        $this->connection = $connection;
        $this->profile = $profile;
    }

    /**
     * @return CollectorInterface[]
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    /**
     * @param Enlight_Controller_Action $controller
     *
     * @return array
     */
    public function collectInformation(Enlight_Controller_Action $controller)
    {
        foreach ($this->collectors as $collector) {
            if ($collector instanceof CollectorInterface) {
                $collector->collect($controller, $this->profile);
            }
        }

        return $this->profile->jsonSerialize();
    }

    public function saveCollectInformation($id, $information, $subrequets = false)
    {
        $information = $this->normalizeArray($information);

        if ($subrequets) {
            $data = $this->cache->fetch($id);
            $data['subrequest'][] = $information;
            $this->cache->save($id, $data);
        } else {
            $this->cache->save($id, $information);

            $this->connection->insert('s_plugin_profiler', [
                'token' => $id,
                'method' => $information['request']['httpMethod'],
                'status' => $information['response']['httpResponse'],
                'ip' => $information['request']['ip'],
                'url' => $information['request']['uri'],
                'time' => date('Y-m-d H:i:s'),
            ]);

            $profileCount = $this->connection->fetchColumn('SELECT COUNT(*) FROM s_plugin_profiler');

            if ($profileCount > $this->pluginConfig['maxProfiles'] && !empty($this->pluginConfig['maxProfiles'])) {
                $deleteProfiles = $profileCount - $this->pluginConfig['maxProfiles'];

                $deleteProfileQuery = $this->connection->createQueryBuilder()
                    ->from('s_plugin_profiler')
                    ->orderBy('time', 'ASC')
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

    /**
     * @param array $variables
     *
     * @return array
     *
     * @author Soner Sayakci <shyim@posteo.de>
     */
    private function normalizeArray(array $variables)
    {
        array_walk_recursive($variables, function (&$value) {
            if (is_object($value)) {
                try {
                    serialize($value);
                } catch (\Exception $e) {
                    $value = null;
                } catch (\Throwable $e) {
                    $value = null;
                }
            }
        });

        return $variables;
    }
}
