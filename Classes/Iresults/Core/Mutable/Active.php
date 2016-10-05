<?php
namespace Iresults\Core\Mutable;

    /*
     * The MIT License (MIT)
     * Copyright (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is
     * furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in
     * all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
     * SOFTWARE.
     */


/**
 * The active mutable class automatically calls the given callbacks or delegate
 * methods if a property is set the first time (it is created) or unset/removed.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Mutable
 */
class Active extends \Iresults\Core\Mutable\AutoExpanding
{
    /**
     * The callback that is invoked when a node is created.
     *
     * @var array<string>
     */
    protected $createCallback;

    /**
     * The callback that is invoked when a node is removed.
     *
     * @var array<string>
     */
    protected $removeCallback;

    /**
     * Sets a properties data.
     *
     * @param    string $key
     * @return    void
     */
    public function __set($key, $value)
    {
        if (!isset($this->_data[$key])) {
            $this->createProperty($key, $value);
        }
        parent::__set($key, $value);
    }

    /**
     * Returns a properties data.
     *
     * @param    string $key
     * @return    mixed
     */
    public function __get($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return null;
        }
    }

    /**
     * Deletes a property.
     *
     * Invoked when unset() is used on inaccessible properties.
     *
     * @param    string $key The property to delete
     * @return    void
     */
    public function __unset($key)
    {
        if (isset($this->_data[$key])) {
            $this->removeProperty($key);
            unset($this->_data[$key]);
        }
    }

    /**
     * Create a new property with the given key and value.
     *
     * @param    string $key   The key for the property which should be created
     * @param    mixed  $value The value for the new property
     * @return    mixed    Returns the result from the delegated calls
     */
    public function createProperty($key, $value)
    {
        if ($this->createCallback) {
            $arguments = array($key, $value, $this);

            return call_user_func_array($this->createCallback, $arguments);
        } else {
            $result = $this->_callMethodIfExists("createCallback");
            if ($result !== self::IR_METHOD_NOT_FOUND) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Remove a the given property.
     *
     * @param    string $key The key for the property which should be removed
     * @return    mixed    Returns the result from the delegated calls
     */
    public function removeProperty($key)
    {
        if ($this->removeCallback) {
            $oldValue = $this->getObjectForKey($key);
            $arguments = array($key, $oldValue, $this);

            return call_user_func_array($this->removeCallback, $arguments);
        } else {
            $result = $this->_callMethodIfExists("removeCallback");
            if ($result !== self::IR_METHOD_NOT_FOUND) {
                return $result;
            }
        }

        return null;
    }

}
