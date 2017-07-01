<?php

namespace ShyimProfiler\Components\Cache;

class CacheFactory
{
    /**
     * @param string $backend
     * @param array  $frontendOptions
     * @param array  $backendOptions
     *
     * @return \Zend_Cache_Core
     */
    public function factory($backend, $frontendOptions = [], $backendOptions = [])
    {
        $backend = $this->createBackend($backend, $backendOptions);
        $cacheCore = $this->createCacheCore($frontendOptions);

        $cacheCore->setBackend($backend);

        \Zend_Locale_Data::setCache($cacheCore);
        \Zend_Db_Table_Abstract::setDefaultMetadataCache($cacheCore);

        return $cacheCore;
    }

    /**
     * @param $backend
     * @param $backendOptions
     *
     * @return \Zend_Cache_Backend
     */
    private function createBackend($backend, $backendOptions)
    {
        if (strtolower($backend) === 'auto') {
            $backend = $this->createAutomaticBackend($backendOptions);
        } else {
            if (strtolower($backend) === 'apc') {
                $backend = 'apcu';
            }

            $backend = \Zend_Cache::_makeBackend($backend, $backendOptions);
        }

        return $backend;
    }

    /**
     * @param array $backendOptions
     *
     * @return \Zend_Cache_Backend
     */
    private function createAutomaticBackend($backendOptions = [])
    {
        if ($this->isApcuAvailable()) {
            $backend = new \Zend_Cache_Backend_Apcu($backendOptions);
        } else {
            $backend = new \Zend_Cache_Backend_File($backendOptions);
        }

        return $backend;
    }

    /**
     * @return bool
     */
    private function isApcuAvailable()
    {
        if (PHP_SAPI === 'cli') {
            return false;
        }

        return extension_loaded('apcu');
    }

    /**
     * @param array $frontendOptions
     *
     * @return Cache
     */
    private function createCacheCore($frontendOptions = [])
    {
        $frontend = new Cache($frontendOptions);

        return $frontend;
    }
}
