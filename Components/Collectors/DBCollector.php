<?php

namespace ShyimProfiler\Components\Collectors;

use Doctrine\DBAL\Logging\DebugStack;
use Enlight_Components_Db_Adapter_Pdo_Mysql;
use Enlight_Controller_Action;
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
     * @var int
     */
    private $executeTime = 0;

    /**
     * DBCollector constructor.
     *
     * @param Enlight_Components_Db_Adapter_Pdo_Mysql $db
     * @param ModelManager                            $modelManager
     *
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function __construct(Enlight_Components_Db_Adapter_Pdo_Mysql $db, ModelManager $modelManager)
    {
        $this->zendProfiler = $db->getProfiler();
        $this->doctrineProfiler = $modelManager->getConfiguration()->getSQLLogger();
    }


    public function getName()
    {
        return 'Database';
    }

    public function collect(Enlight_Controller_Action $controller)
    {
        $totalQueriesZend = $this->zendProfiler->getTotalNumQueries();
        $totalQueriesDoctrine = count($this->doctrineProfiler->queries);
        $this->executeTime = $this->zendProfiler->getTotalElapsedSecs();

        $this->getAllQuerys();

        $result = [
            'db' => [
                'totalQueries' => $totalQueriesZend + $totalQueriesDoctrine,
                'queryTime'    => $this->executeTime,
                'sqls'         => $this->executedQuerys,
            ],
        ];

        return $result;
    }

    public function getToolbarTemplate()
    {
        return '@Profiler/toolbar/db.tpl';
    }

    private function getAllQuerys()
    {
        foreach ($this->doctrineProfiler->queries as $query) {
            $this->executedQuerys[] = [
                'sql'       => $query['sql'],
                'params'    => $query['params'],
                'execution' => $query['executionMS'],
            ];
            $this->executeTime += $query['executionMS'];
        }

        foreach ($this->zendProfiler->getQueryProfiles() as $queryProfile) {
            $this->executedQuerys[] = [
                'sql'       => $queryProfile->getQuery(),
                'params'    => $queryProfile->getQueryParams(),
                'execution' => $queryProfile->getElapsedSecs(),
            ];
        }
    }
}
