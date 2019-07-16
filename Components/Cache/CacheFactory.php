<?php

namespace FroshProfiler\Components\Cache;

class CacheFactory
{
    public function factory(string $backend, array $frontendOptions = [], array $backendOptions = []): \Zend_Cache_Core
    {
        $backend = $this->createBackend($backend, $backendOptions);
        $cacheCore = $this->createCacheCore($frontendOptions);

        $cacheCore->setBackend($backend);

        \Zend_Locale_Data::setCache($cacheCore);
        \Zend_Db_Table_Abstract::setDefaultMetadataCache($cacheCore);

        return $cacheCore;
    }

    private function createBackend(string $backend, array $backendOptions): \Zend_Cache_Backend
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

    private function createAutomaticBackend(array $backendOptions = []): \Zend_Cache_Backend
    {
        if ($this->isApcuAvailable()) {
            $backend = new \Zend_Cache_Backend_Apcu($backendOptions);
        } else {
            $backend = new \Zend_Cache_Backend_File($backendOptions);
        }

        return $backend;
    }

    private function isApcuAvailable(): bool
    {
        if (PHP_SAPI === 'cli') {
            return false;
        }

        return extension_loaded('apcu');
    }

    private function createCacheCore(array $frontendOptions = []): Cache
    {
        $frontend = new Cache($frontendOptions);

        return $frontend;
    }
}
