<?php

namespace FroshProfiler\Components\Cache;

use Zend_Cache;
use Zend_Cache_Core;

/**
 * Class Cache
 */
class Cache extends Zend_Cache_Core
{
    /**
     * @var int
     */
    public $calls;

    /**
     * @var int
     */
    public $hit = 0;

    /**
     * @var int
     */
    public $hitMissed = 0;

    /**
     * @var int
     */
    public $read = 0;

    /**
     * @var int
     */
    public $write = 0;

    /**
     * @var int
     */
    public $delete = 0;

    /**
     * @var float
     */
    public $time = 0;

    /**
     * @var string
     */
    protected $_lastId;

    /**
     * @param string $id
     * @param bool   $doNotTestCacheValidity
     * @param bool   $doNotUnserialize
     *
     * @return bool|false|mixed|string
     */
    public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        ++$this->calls;
        $time = microtime(true);
        if (!$this->_options['caching']) {
            return false;
        }
        $id = $this->_id($id); // cache id may need prefix
        $this->_lastId = $id;
        self::_validateIdOrTag($id);

        $this->_log("Zend_Cache_Core: load item '{$id}'", 7);
        $data = $this->_backend->load($id, $doNotTestCacheValidity);
        if ($data === false) {
            ++$this->hitMissed;
            // no cache available
            return false;
        }
        ++$this->hit;
        ++$this->read;
        $this->time += (microtime(true) - $time);
        if ((!$doNotUnserialize) && $this->_options['automatic_serialization']) {
            // we need to unserialize before sending the result
            return unserialize($data);
        }

        return $data;
    }

    /**
     * @param array  $data
     * @param string $id
     * @param array  $tags
     * @param bool   $specificLifetime
     * @param int    $priority
     *
     * @return bool
     */
    public function save($data, $id = null, $tags = [], $specificLifetime = false, $priority = 8)
    {
        ++$this->calls;
        $time = microtime(true);
        if (!$this->_options['caching']) {
            return true;
        }
        if ($id === null) {
            $id = $this->_lastId;
        } else {
            $id = $this->_id($id);
        }
        self::_validateIdOrTag($id);
        self::_validateTagsArray($tags);
        if ($this->_options['automatic_serialization']) {
            // we need to serialize datas before storing them
            $data = serialize($data);
        } else {
            if (!is_string($data)) {
                Zend_Cache::throwException('Datas must be string or set automatic_serialization = true');
            }
        }

        // automatic cleaning
        if ($this->_options['automatic_cleaning_factor'] > 0) {
            $rand = rand(1, $this->_options['automatic_cleaning_factor']);
            if ($rand == 1) {
                //  new way                 || deprecated way
                if ($this->_extendedBackend || method_exists($this->_backend, 'isAutomaticCleaningAvailable')) {
                    $this->_log('Zend_Cache_Core::save(): automatic cleaning running', 7);
                    $this->clean(Zend_Cache::CLEANING_MODE_OLD);
                } else {
                    $this->_log('Zend_Cache_Core::save(): automatic cleaning is not available/necessary with current backend', 4);
                }
            }
        }

        $this->_log("Zend_Cache_Core: save item '{$id}'", 7);
        if ($this->_options['ignore_user_abort']) {
            $abort = ignore_user_abort(true);
        }
        $result = $this->_backend->save($data, $id, $tags, $specificLifetime);
        if ($this->_options['ignore_user_abort'] && isset($abort) && $abort) {
            ignore_user_abort($abort);
        }

        ++$this->write;

        $this->time += (microtime(true) - $time);

        if (!$result) {
            // maybe the cache is corrupted, so we remove it !
            $this->_log("Zend_Cache_Core::save(): failed to save item '{$id}' -> removing it", 4);
            $this->_backend->remove($id);

            return false;
        }

        if ($this->_options['write_control']) {
            $data2 = $this->_backend->load($id, true);
            if ($data != $data2) {
                $this->_log("Zend_Cache_Core::save(): write control of item '{$id}' failed -> removing it", 4);
                $this->_backend->remove($id);

                return false;
            }
        }

        return true;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function remove($id)
    {
        $time = microtime(true);
        ++$this->calls;
        if (!$this->_options['caching']) {
            return true;
        }
        $id = $this->_id($id); // cache id may need prefix
        self::_validateIdOrTag($id);

        ++$this->delete;

        $this->_log("Zend_Cache_Core: remove item '{$id}'", 7);
        $remove = $this->_backend->remove($id);

        $this->time += (microtime(true) - $time);

        return $remove;
    }
}
