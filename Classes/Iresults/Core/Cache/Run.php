<?php

namespace Iresults\Core\Cache;


/**
 * Run adapter for the cache. Which only provides caching for runtime.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Core
 */
class Run extends \Iresults\Core\Cache\AbstractCache
{
    /**
     * @var array The cache array.
     */
    static protected $_cache = [];

    /**
     * Returns the object at the given key.
     *
     * @param string $key
     * @return    mixed
     */
    public function getObjectForKey($key)
    {
        if (array_key_exists($key, self::$_cache)) {
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
        if (array_key_exists($key, self::$_cache)) {
            unset(self::$_cache[$key]);
        }
    }

    /**
     * Removes the complete cache.
     *
     * @return    void
     */
    public function clear()
    {
        //unset(self::$_cache);
        self::$_cache = [];
        $this->pd("Cache cleared.");
    }

    /**
     * Returns the scope of the cache.
     *
     * The Run cache is allways language dependent.
     *
     * @return    integer    The current scope as one of the SCOPE constants
     */
    public function getScope()
    {
        return self::SCOPE_LANGUAGE;
    }
}
