<?php

namespace ShyimProfiler\Components\Collectors;

class DBCollector implements CollectorInterface
{
    /** @var \Zend_Db_Profiler */
    private $zendProfiler;
    /** @var \Doctrine\DBAL\Logging\DebugStack $doctrineProfiler */
    private $doctrineProfiler;
    private $executedQuerys = [];
    private $executeTime = 0;

    public function __construct()
    {
        $this->zendProfiler = Shopware()->Db()->getProfiler();
        $this->doctrineProfiler = Shopware()->Models()->getConfiguration()->getSQLLogger();
    }

    public function getName()
    {
        return 'Database';
    }

    public function collect(\Enlight_Controller_Action $controller)
    {
        $totalQueriesZend = $this->zendProfiler->getTotalNumQueries();
        $totalQueriesDoctrine = count($this->doctrineProfiler->queries);
        $this->executeTime = $this->zendProfiler->getTotalElapsedSecs();

        $this->getAllQuerys();

        $result = [
            'db' => [
                'totalQueries' => $totalQueriesZend + $totalQueriesDoctrine,
                'queryTime'    => $this->executeTime,
                'sqls' => $this->executedQuerys
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
