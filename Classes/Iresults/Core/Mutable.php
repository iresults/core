<?php

namespace Iresults\Core;

use Iresults\Core\Exception\UndefinedMethod;
use Iterator;


/**
 * The base class for mutable objects.
 * Iresults mutable classes conform to the \Iresults\Core\KVC-interface.
 */
class Mutable extends \Iresults\Core\Model implements \Countable, \Iterator, \ArrayAccess, \JsonSerializable
{
    /**
     * The virtual class name
     *
     * @var string
     */
    protected $__virtualClass = '(not set)';

    /**
     * The dictionary holding the data.
     *
     * @var array
     */
    protected $_data = [];

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* INITIALIZATION        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Initialize the instance with the data from the given array.
     *
     * @param array|\Iterator The source from which to read the properties
     *
     * @return    \Iresults\Core\Mutable The mutable object
     */
    public function initWithArray($array)
    {
        $this->setPropertiesFromArray($array);

        return $this;
    }

    /**
     * The constructor
     *
     * @param array $parameters Optional parameters to pass to the constructor
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
        if (get_class($this) != __CLASS__) {
            $this->__virtualClass = get_class($this);
        }
    }

    /**
     * Set all properties from the array.
     * Traverses the given source and tries to handle each key-value-pair as a new
     * property value for that key.
     *
     * @param array   $source            The input array
     * @param boolean $prepareSourceKeys Indicates if the _prepareSourceKeys method should be invoked for the source
     * @param string  $prefix            Optional prefix to add to the keys
     * @return    void
     */
    public function setPropertiesFromArray($source, $prepareSourceKeys = false, $prefix = '')
    {
        if ($prepareSourceKeys) {
            $source = $this->_prepareSourceKeys($source);
        }
        foreach ($source as $propertyName => $value) {
            if ($propertyName === 'tstamp' OR $propertyName === 'crdate' OR substr($propertyName, -5) === '_date') {
                $value = new \Iresults\Core\DateTime($value);
            }
            $propertyName = $prefix . $propertyName;
            $this->setObjectForKeyPath($propertyName, $value);
        }
    }

    /**
     * Sets a properties data.
     *
     * @param string $key
     * @return    void
     */
    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Sets a properties data.
     *
     * @param string $key
     * @return    void
     */
    public function setData($key, $value)
    {
        //$key = $this->_functionNameToPropertyName($key);
        if (func_num_args() < 2) {
            throw new \InvalidArgumentException('setData requires two arguments.', 1322234376);
        }
        $this->__set($key, $value);
    }

    /**
     * Sets an object/value for the given key.
     *
     * @param string $key   The key to set
     * @param mixed  $value The new value
     * @return    void
     */
    public function setObjectForKey($key, $value)
    {
        $this->__set($key, $value);
    }

    /**
     * Returns a properties data.
     *
     * @param string $key
     * @return    mixed
     */
    public function __get($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return $this->getObjectForUndefinedKey($key);
        }
    }

    /**
     * Returns a properties data.
     *
     * @param string $key
     * @return    mixed
     */
    public function getData($key = null)
    {
        if (func_num_args() < 1) {
            return $this->toArray();
        } else {
            return $this->__get($key);
        }
    }

    /**
     * Returns an object/value for the given key.
     *
     * @param string $key The key/property name to fetch
     * @return    mixed
     */
    public function getObjectForKey($key)
    {
        return $this->__get($key);
    }

    /**
     * Called if no object was found for the given property key.for the give
     *
     * @param string $key The name of the undefined property
     * @return    mixed    Returns a substitue value
     * @throws InvalidArgumentException on default.
     */
    public function getObjectForUndefinedKey($key)
    {
        return null;
    }

    /**
     * Deletes a property.
     *
     * Invoked when unset() is used on inaccessible properties.
     *
     * @param string $key The property to delete
     * @return    void
     */
    public function __unset($key)
    {
        if (isset($this->_data[$key])) {
            unset($this->_data[$key]);
        }
    }

    /**
     * Deletes a property.
     *
     * @param string $key The property to delete
     * @return    void
     */
    public function unsetData($key)
    {
        $this->__unset($key);
    }

    /**
     * Removes the object with the given key.
     *
     * @param string $key
     * @return    void
     */
    public function removeObjectForKey($key)
    {
        $this->unsetData($key);
    }

    /**
     * Tests if a property exists.
     *
     * Invoked by isset() or empty() for inaccessible properties.
     *
     * @param string $key The property name
     * @return    boolean    TRUE if the property exists, otherwise FALSE
     */
    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }

    /**
     * Returns the data array.
     *
     * @return    array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * Returns the virtual class of the object
     *
     * @return    string
     */
    public function getClass()
    {
        return $this->__virtualClass;
    }

    /**
     * Sets the virtual class of the object
     *
     * @param string $newClass
     */
    public function setClass($newClass)
    {
        $this->__virtualClass = $newClass;
    }

    /**
     * Magic access to properties.
     */
    public function __call($name, array $arguments)
    {
        if (substr($name, 0, 3) == 'set') {
            $propertyName = $this->_functionNameToPropertyName($name);
            $value = current($arguments);
            $this->__set($propertyName, $value);

            return null;
        } elseif (substr($name, 0, 3) == 'get') {
            if (!empty($arguments)) {
                throw new UndefinedMethod('You called a virtual getter method with an argument.', 1319795026);
            }
            $propertyName = $this->_functionNameToPropertyName($name);

            return $this->__get($propertyName);
        } else {
            throw new UndefinedMethod(
                'You called the method \'' . $name . '\', which isn\'t a getter or setter.',
                1323971571
            );
        }
    }

    /**
     * Execute a method
     *
     * @param string $name
     * @return mixed|null
     */
    public function execute($name)
    {
        if (!$name) {
            return null;
        }

        return $this->__call($name, []);
    }

    /**
     * The description function.
     *
     * @return    string
     */
    public function description()
    {
        return 'object(' . get_class($this) . ') \'virtualClass => \'' . $this->getClass(
            ) . '\'\n) {\n\t' . Iresults::descriptionOfValue($this->_data) . '}';
    }

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* HELPER FUNCTIONS WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Makes the first character of the given string lowercase.
     *
     * @param string $string
     * @return    string
     */
    protected function _lcfirst($string)
    {
        return strtolower(substr($string, 0, 1)) . substr($string, 1);
    }

    /**
     * Converts a functionname to a property name.
     *
     * @param string $functionName The function name
     * @return    string    The property name
     */
    protected function _functionNameToPropertyName($name)
    {
        $opperator = substr($name, 0, 3);
        if ($opperator == 'get' || $opperator == 'set') {
            $name = substr($name, 3);
        }
        if (class_exists('\Iresults\Core\Tools\StringTool')) {
            $name = \Iresults\Core\Tools\StringTool::camelCaseToLowerCaseUnderscored($name);
        } else {
            $name = $this->_lcfirst($name);
        }

        return $name;
    }

    /**
     * Cleans up the instance.
     *
     * @return    void
     */
    public function cleanup()
    {
    }

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ITERATOR       WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    public function current()
    {
        return current($this->_data);
    }

    public function key()
    {
        return key($this->_data);
    }

    public function next()
    {
        return next($this->_data);
    }

    public function rewind()
    {
        return reset($this->_data);
    }

    public function valid()
    {
        return (key($this->_data) !== null);
    }
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ARRAY ACCESS   WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->getData($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->setData($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->unsetData($offset);
    }
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* COUNTABLE      WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    public function count()
    {
        return count($this->_data);
    }
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* JSON SERIALIZABLE    WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    public function jsonSerialize()
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            return $this->toArray();
        }

        return \Iresults\Core\Helpers\SerializationHelper::objectToArray($this);
    }
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Factory method: Returns a mutable object representing the data from the
     * given URL.
     *
     * @param string $url URL of the file to read
     * @return    \Iresults\Core\Mutable
     */
    static public function mutableWithContentsOfUrl($url)
    {
        $mutable = null;
        if (strtolower(substr($url, -4)) == '.xml') {

            $mutable = new \Iresults\Core\Mutable\Xml();
            $mutable->initWithContentsOfUrl($url);
        } elseif (strtolower(substr($url, -4)) == '.csv') {

            $mutable = new \Iresults\Core\Mutable\Csv();
            $mutable->initWithContentsOfUrl($url);
        } elseif (strtolower(substr($url, -5)) == '.yaml') {

            $mutable = \Iresults\Core\Mutable\Yaml::mutableWithContentsOfUrl($url);
        }

        return $mutable;
    }

    /**
     * Factory method: Returns a mutable object with the data from the given array.
     *
     * @param array|Iterator The source from which to read the properties
     * @return    \Iresults\Core\Mutable The mutable object
     */
    static public function mutableWithArray($array)
    {
        $mutable = self::mutable();
        $mutable->initWithArray($array);

        return $mutable;
    }

    /**
     * Factory method: Returns a mutable object with the data from the given array.
     *
     * @param Mutable $mutable The source from which to read the properties
     * @return    \Iresults\Core\Mutable The mutable object
     */
    static public function mutableWithMutable($mutable)
    {
        $data = &$mutable->getData();
        $newMutable = self::mutable();
        $newMutable->initWithArray($data);

        return $newMutable;
    }

    /**
     * Factory method: Returns a mutable object with the data from the given array.
     *
     * @param array|Iterator The source from which to read the properties
     * @return    \Iresults\Core\Mutable The mutable object
     */
    static public function mutableWithStdClass($object)
    {
        $mutable = self::mutable();
        $array = get_object_vars($object);
        $mutable->initWithArray($array);

        return $mutable;
    }

    /**
     * Factory method: Returns a new mutable object without any data.
     *
     * @return    \Iresults\Core\Mutable The mutable object
     */
    static public function mutable()
    {
        return self::alloc();
    }
}
