<?php

namespace FroshProfiler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Shopware_Controllers_Backend_Cache;
use Shopware_Controllers_Backend_Performance;

/**
 * Class CacheSubscriber
 */
class CacheSubscriber implements SubscriberInterface
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $templateDir;

    /**
     * CacheSubscriber constructor.
     *
     * @param string $cacheDir
     * @param string $templateDir
     */
    public function __construct($cacheDir, $templateDir)
    {
        $this->cacheDir = $cacheDir;
        $this->templateDir = $templateDir;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Backend_Cache' => 'onPostDispatchBackendCache',
            'Enlight_Controller_Action_PostDispatch_Backend_Performance' => 'onPostDispatchBackendPerformance',
        ];
    }

    public function onPostDispatchBackendPerformance(Enlight_Event_EventArgs $args): void
    {
        /** @var Shopware_Controllers_Backend_Performance $subject */
        $subject = $args->get('subject');

        if ($subject->Request()->getActionName() === 'load') {
            $subject->View()->addTemplateDir($this->templateDir);
            $subject->View()->extendsTemplate('backend/FroshProfiler/performance/view/tabs/cache/form.js');
        }
    }

    public function onPostDispatchBackendCache(Enlight_Event_EventArgs $args): void
    {
        /** @var Shopware_Controllers_Backend_Cache $subject */
        $subject = $args->get('subject');

        if ($subject->Request()->getActionName() == 'getInfo') {
            $data = $subject->View()->getAssign('data');

            $dir = $subject->get('shopware.cache_manager')->getDirectoryInfo($this->cacheDir);
            $dir['name'] = 'Profiler';

            $data[] = $dir;

            $subject->View()->assign('data', $data);
        } elseif ($subject->Request()->getActionName() == 'clearCache') {
            $cacheParams = $subject->Request()->getParam('cache');

            if (!empty($cacheParams['profiler'])) {
                $this->clearDirectory($this->cacheDir);

                $subject->get('dbal_connection')->executeQuery('TRUNCATE TABLE s_plugin_profiler');
            }
        }
    }

    private function clearDirectory(string $dir): void
    {
        if (!file_exists($dir)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var \SplFileInfo $path */
        foreach ($iterator as $path) {
            if ($path->getFilename() === '.gitkeep') {
                continue;
            }

            if ($path->isDir()) {
                rmdir($path->__toString());
            } else {
                if (!$path->isFile()) {
                    continue;
                }
                unlink($path->__toString());
            }
        }
    }
}
