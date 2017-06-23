<?php

namespace Iresults\Core;

use Iresults\Core\Exception\UndefinedMethod;
use Iresults\Core\Helpers\ObjectHelper;
use Iresults\Core\Tools\StringTool;

/**
 * A mutable data object
 */
class DataObject implements \ArrayAccess, \JsonSerializable, KVCInterface
{
    /**
     * The virtual class name
     *
     * @var string
     */
    protected $virtualClass = '';

    /**
     * The dictionary holding the data
     *
     * @var array
     */
    protected $data = [];

    /**
     * The constructor
     *
     * @param array  $data         Data to store in the object
     * @param string $virtualClass Virtual class name for the object
     */
    public function __construct(array $data = [], $virtualClass = '')
    {
        $this->data = $data;
        $this->virtualClass = $virtualClass ?: get_class($this);
    }

    /**
     * Returns an object/value for the given key
     *
     * @param string $key The key/property name to fetch
     * @return mixed
     */
    public function getObjectForKey($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Returns the value of the property at the given key path
     *
     * @param string $propertyPath The property key path to resolve in the format "object.property"
     * @return    mixed
     */
    public function getObjectForKeyPath($propertyPath)
    {
        return ObjectHelper::getObjectForKeyPathOfObject($propertyPath, $this);
    }

    /**
     * Sets an object/value for the given key
     *
     * @param string $key   The key to set
     * @param mixed  $value The new value
     * @return void
     */
    public function setObjectForKey($key, $value)
    {
        if (!is_scalar($key)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument key must be of type "string", "%s" given',
                    is_object($key) ? get_class($key) : gettype($key)
                )
            );
        }
        $this->data[(string)$key] = $value;
    }

    /**
     * Sets the value for the property identified by a given key path.
     *
     * @param string $propertyPath The property key path in the form (object.property)
     * @param mixed  $object       The new value to assign
     * @return    void
     */
    public function setObjectForKeyPath($propertyPath, $object)
    {
        ObjectHelper::setObjectForKeyPathOfObject($propertyPath, $object, $this);
    }

    /**
     * Removes the object with the given key
     *
     * @param string $key
     * @return void
     */
    public function removeObjectForKey($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Returns a properties data
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getObjectForKey($key);
    }

    /**
     * Sets a properties data
     *
     * @param string $key
     * @param        $value
     */
    public function __set($key, $value)
    {
        $this->setObjectForKey($key, $value);
    }

    /**
     * Deletes a property
     *
     * Invoked when unset() is used on inaccessible properties
     *
     * @param string $key The property to delete
     * @return void
     */
    public function __unset($key)
    {
        $this->removeObjectForKey($key);
    }

    /**
     * Tests if a property exists
     *
     * Invoked by isset() or empty() for inaccessible properties
     *
     * @param string $key The property name
     * @return boolean    TRUE if the property exists, otherwise FALSE
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Returns the data array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Returns the virtual class of the object
     *
     * @return string
     */
    public function getClass()
    {
        return $this->virtualClass;
    }

    /**
     * Sets the virtual class of the object
     *
     * @param string $newClass
     */
    public function setClass($newClass)
    {
        $this->virtualClass = $newClass;
    }

    /**
     * Magic access to properties
     *
     * @param string $name
     * @param array  $arguments
     * @return mixed|null
     * @throws UndefinedMethod
     */
    public function __call($name, array $arguments)
    {
        if (substr($name, 0, 3) == 'set') {
            $this->setObjectForKey($this->functionNameToPropertyName($name), array_shift($arguments));
        } elseif (substr($name, 0, 3) == 'get') {
            if (!empty($arguments)) {
                throw new UndefinedMethod('You called a virtual getter method with an argument.', 1319795026);
            }

            return $this->getObjectForKey($this->functionNameToPropertyName($name));
        } else {
            throw new UndefinedMethod("You called the method '$name', which is no getter or setter", 1323971571);
        }

        return null;
    }

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* HELPER FUNCTIONS WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Converts a function name to a property name
     *
     * @param string $name The function name
     * @return string    The property name
     */
    protected function functionNameToPropertyName($name)
    {
        $operator = substr($name, 0, 3);
        if ($operator == 'get' || $operator == 'set') {
            $name = substr($name, 3);
        }

        return StringTool::camelCaseToLowerCaseUnderscored($name);
    }

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ARRAY ACCESS   WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->getObjectForKey($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->setObjectForKey($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->removeObjectForKey($offset);
    }
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* JSON SERIALIZABLE    WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
