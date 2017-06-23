<?php

namespace Iresults\Core;

/**
 * Interface for classes that provide key based access to properties
 */
interface KVCInterface
{
    /**
     * Returns the value at the given key
     *
     * @param string $key
     * @return mixed
     */
    public function getObjectForKey($key);

    /**
     * Stores the value of $object at the given key
     *
     * @param string $key
     * @param mixed  $object
     * @return void
     */
    public function setObjectForKey($key, $object);

    /**
     * Removes the object with the given key.
     *
     * @param string $key
     * @return void
     */
    public function removeObjectForKey($key);
}
