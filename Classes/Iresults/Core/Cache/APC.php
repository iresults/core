<?php

namespace Iresults\Core\Cache;


/**
 * APC adapter for the cache
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Core
 */
class APC extends \Iresults\Core\Cache\AbstractCache
{
    /**
     * Returns the object at the given key.
     *
     * @param string $key
     * @return    mixed
     */
    public function getObjectForKey($key)
    {
        $key = $key . self::_getLanguageSuffix();
        if ($value = apc_fetch($key)) {
            return $value;
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
        $key = $key . self::_getLanguageSuffix();
        apc_store($key, $object);
    }

    /**
     * Removes the object with the given key.
     *
     * @param string $key
     * @return    void
     */
    public function removeObjectForKey($key)
    {
        $key = $key . self::_getLanguageSuffix();
        apc_delete($key);
    }

    /**
     * Removes the complete cache.
     *
     * @return    void
     */
    public function clear()
    {
        $result = apc_clear_cache('user');
        //$result = apc_clear_cache();
        $this->debug('Cache cleared. Result= ' . $result);
    }
}
