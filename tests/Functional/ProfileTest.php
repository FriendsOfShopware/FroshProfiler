<?php

use Doctrine\Common\Cache\FilesystemCache;
use Frosh\ClassicPhpunitBridge\Test\ControllerTest;

class ProfileTest extends ControllerTest
{
    /**
     * @var FilesystemCache
     */
    private $cache;

    /**
     * @var array
     */
    private $pluginConfig;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function setUp(): void
    {
        parent::setUp();

        $this->pluginConfig = Shopware()->Container()->get('frosh_profiler.config');
        $this->cache = Shopware()->Container()->get('frosh_profiler.cache');
        $this->connection = Shopware()->Container()->get('dbal_connection');
        Shopware()->Container()->get('frosh_profiler.current.profile')->reset();
        $this->dispatch('/');
    }

    public function testSmartyVariables(): void
    {
        $templateAssigns = $this->View()->getAssign();

        $this->assertArrayHasKey('sProfiler', $templateAssigns);
        $this->assertArrayHasKey('sProfilerID', $templateAssigns);
        $this->assertArrayHasKey('sProfilerTime', $templateAssigns);
        $this->assertArrayHasKey('sProfilerCollectors', $templateAssigns);
    }

    public function testProfileSaved(): void
    {
        $id = $this->View()->getAssign('sProfilerID');

        $this->assertTrue($this->cache->contains($id));
        $this->assertEquals($this->connection->fetchColumn('SELECT 1 FROM s_plugin_profiler WHERE token = ?', [$id]), 1);

        $this->assertNotEmpty(Shopware()->Container()->get('frosh_profiler.current.profile')->getId());
    }

    public function testProfileContent(): void
    {
        $id = $this->View()->getAssign('sProfilerID');
        $profile = $this->cache->fetch($id);

        // Response
        $this->assertEquals(200, $profile['response']['httpResponse']);

        // Request
        $this->assertEquals('frontend', $profile['request']['moduleName']);
        $this->assertEquals('index', $profile['request']['controllerName']);
        $this->assertEquals('index', $profile['request']['actionName']);
        $this->assertEquals('GET', $profile['request']['httpMethod']);

        // Events
        $this->assertGreaterThan(0, $profile['events']['eventAmount']);
    }
}
