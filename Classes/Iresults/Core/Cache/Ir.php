<?php

namespace Iresults\Core\Cache;

use Iresults\Core\System\Lock;


/**
 * Ir adapter for the cache. Writes the cache to a file.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Core
 */
class Ir extends \Iresults\Core\Cache\AbstractCache
{
    /**
     * The cache itself
     *
     * @var array
     */
    static protected $_cache = [];

    /**
     * Indicates if the shutdown handler was registered
     *
     * @var boolean
     */
    static protected $_didInstallShutdownHandler = false;

    /**
     * The file name of the cache file
     *
     * @var string
     */
    static protected $_fileName = 'IRESULTS_CACHE';

    /**
     * A lock to provide errors if multiple processes wont the write the cache file
     *
     * @var \Iresults\Core\System\AbstractLock
     */
    static protected $_lock = null;

    /**
     * Indicates if the cache was changed. If not, the cache file does not have to be written
     *
     * @var boolean
     */
    static protected $_cacheWasChanged = false;

    /**
     * The constructor
     */
    public function __construct()
    {
        if (!self::$_lock) {
            self::$_lock = new Lock('ir_cache_lock' . self::_getLanguageSuffix());
        }

        parent::__construct();

        if (!self::$_didInstallShutdownHandler) {
            register_shutdown_function(['\Iresults\Core\Cache\Ir', '_writeCacheFile']);
            self::$_didInstallShutdownHandler = true;
        }
        if (!self::$_cache || empty(self::$_cache)) {
            self::_readCacheFile();
        }


        return $this;
    }

    /**
     * Returns the object at the given key.
     *
     * @param string $key
     * @return    mixed
     */
    public function getObjectForKey($key)
    {
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        } else {
            return null;
        }
    }

    /**
     * Stores the value of $object at the given key.
     *
     * @param string $key
     * @param mixed  $object
     * @return    void
     */
    public function setObjectForKey($key, $object)
    {
        self::$_cacheWasChanged = true;
        self::$_cache[$key] = $object;
    }

    /**
     * Removes the object with the given key.
     *
     * @param string $key
     * @return    void
     */
    public function removeObjectForKey($key)
    {
        if (isset(self::$_cache[$key])) {
            unset(self::$_cache[$key]);
            self::$_cacheWasChanged = true;
        }
    }

    /**
     * Removes the complete cache.
     *
     * @return    void
     */
    public function clear()
    {
        self::$_cache = [];
        $result = true;
        $path = self::getCacheDir() . self::$_fileName . '*';
        $foundPaths = glob($path);

        if (!$foundPaths || empty($foundPaths)) {
            $this->pd('No matching cache files found for pattern "' . $path . '"');

            return;
        }

        self::$_lock->lock();
        foreach ($foundPaths as $onePath) {
            if (!unlink($onePath)) {
                $this->pd('Cache could not be cleared because the cache file $onePath couldn\'t be deleted');
                $result = false;
            } else {
                $this->pd('Cache file "' . $onePath . '" deleted');
            }
        }
        self::$_lock->unlock();

        if ($result) {
            $this->pd('Cache cleared');
        }
    }

    /**
     * If the object is destructed and uncommited changes exist, write the cache
     * file.
     *
     * @return    void
     */
    public function __destruct()
    {
        if (self::$_cacheWasChanged) {
            self::_writeCacheFile();
        }
    }

    /**
     * Reads the cache from the cache file.
     *
     * @return    void
     */
    static public function _readCacheFile()
    {
        $path = self::getCacheDir() . self::$_fileName . self::_getLanguageSuffix();
        if (!file_exists($path)) {
            return;
        }
        $contents = file_get_contents($path);
        if (!$contents) {
            return;
        }

        $temp = unserialize($contents);
        if ($temp === false) {
            return;
        }
        self::$_cache = $temp;
    }

    /**
     * Writes the cache to a cache file.
     *
     * @return    void
     */
    static public function _writeCacheFile()
    {
        if (!self::$_cache || empty(self::$_cache)) {
            return;
        } elseif (!self::$_cacheWasChanged) {
            \Iresults\Core\Iresults::pd('Cache was not changed');

            return;
        }
        $path = self::getCacheDir() . self::$_fileName . self::_getLanguageSuffix();
        $contents = serialize(self::$_cache);

        self::$_lock->tryLock();

        $fh = @fopen($path, 'wb');
        if (!$fh) {
            $msg = 'Couldn\'t open file "' . $path . '" for writing the cache information';
            trigger_error($msg, E_USER_WARNING);

            return;
        }

        if (fwrite($fh, $contents) === false) {
            $msg = 'Writing cache to file "' . $path . '" failed';
            trigger_error($msg, E_USER_WARNING);
        }
        fclose($fh);

        self::$_lock->unlock();
        self::$_cacheWasChanged = false;
    }

    /**
     * Returns the path to the cache directory.
     *
     * @return    string
     */
    static public function getCacheDir()
    {
        return \Iresults\Core\Iresults::getTempPath();
    }
}
