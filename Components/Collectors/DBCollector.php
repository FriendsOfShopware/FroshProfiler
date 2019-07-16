<?php

namespace FroshProfiler\Components\Collectors;

use Doctrine\DBAL\Logging\DebugStack;
use Enlight_Components_Db_Adapter_Pdo_Mysql;
use Enlight_Controller_Action;
use FroshProfiler\Components\Struct\Profile;
use Shopware\Components\Model\ModelManager;
use Zend_Db_Profiler;

class DBCollector implements CollectorInterface
{
    /**
     * @var Zend_Db_Profiler
     */
    private $zendProfiler;

    /**
     * @var DebugStack
     */
    private $doctrineProfiler;

    /**
     * @var array
     */
    private $executedQuerys = [];

    /**
     * @var float
     */
    private $executeTime = 0;

    /**
     * DBCollector constructor.
     *
     * @param Enlight_Components_Db_Adapter_Pdo_Mysql $db
     * @param ModelManager                            $modelManager
     */
    public function __construct(Enlight_Components_Db_Adapter_Pdo_Mysql $db, ModelManager $modelManager)
    {
        $this->zendProfiler = $db->getProfiler();

        /** @var DebugStack $sqlLogger */
        $sqlLogger = $modelManager->getConfiguration()->getSQLLogger();
        $this->doctrineProfiler = $sqlLogger;
    }

    public function getName(): string
    {
        return 'Database';
    }

    public function collect(Enlight_Controller_Action $controller, Profile $profile): void
    {
        $totalQueriesZend = $this->zendProfiler->getTotalNumQueries();
        $totalQueriesDoctrine = count($this->doctrineProfiler->queries);
        $this->executeTime = $this->zendProfiler->getTotalElapsedSecs();

        $this->getAllQuerys();

        $profile->setDbQueries([
            'totalQueries' => $totalQueriesZend + $totalQueriesDoctrine,
            'queryTime' => $this->executeTime,
            'sqls' => $this->executedQuerys,
        ]);
    }

    public function getToolbarTemplate(): ?string
    {
        return '@Toolbar/toolbar/db.tpl';
    }

    private function getAllQuerys(): void
    {
        foreach ($this->doctrineProfiler->queries as $query) {
            $this->executedQuerys[] = [
                'sql' => $query['sql'],
                'params' => $query['params'],
                'execution' => $query['executionMS'],
            ];
            $this->executeTime += $query['executionMS'] / 1000;
        }

        foreach ($this->zendProfiler->getQueryProfiles() as $queryProfile) {
            $this->executedQuerys[] = [
                'sql' => $queryProfile->getQuery(),
                'params' => $queryProfile->getQueryParams(),
                'execution' => $queryProfile->getElapsedSecs() * 1000,
            ];
        }
    }
}
