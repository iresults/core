<?php

namespace Iresults\Core;


/**
 * The iresults registry.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Core
 */
class Registry extends \Iresults\Core\Singleton implements \Iresults\Core\KVCInterface
{
    /**
     * The storage for the registered data.
     *
     * @var array
     */
    static protected $_internalStorage = [];

    /**
     * Returns the object at the given key.
     *
     * @param string $key
     * @return    mixed
     */
    public function getObjectForKey($key)
    {
        if (isset(self::$_internalStorage[$key])) {
            return self::$_internalStorage[$key];
        }

        return null;
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
        self::$_internalStorage[$key] = $object;
    }

    /**
     * Removes the object with the given key.
     *
     * @param string $key
     * @return    void
     */
    public function removeObjectForKey($key)
    {
        unset(self::$_internalStorage[$key]);
    }

    /**
     * If only one parameter is passed, the registry will be searched for that
     * key, if two arguments are given the first one will be used as key and the
     * second one will be assigned for it.
     *
     * @param string $key    The key to fetch/set
     * @param mixed  $object The value/object to set
     * @return    mixed    Returns the object for the given key
     */
    static public function registry($key, $object = null)
    {
        if (func_num_args() > 1) {
            self::$_internalStorage[$key] = $object;

            return $object;
        }

        if (isset(self::$_internalStorage[$key])) {
            return self::$_internalStorage[$key];
        }

        return null;
    }

    /**
     * The registry must not be serialized.
     *
     * @return    void
     */
    public function __sleep()
    {
        throw new Exception("The registry must not be serialized.", 1314028891);
    }
}
