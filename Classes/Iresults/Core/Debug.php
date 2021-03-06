<?php

namespace Iresults\Core;


/**
 * Prints a debug output
 */
class Debug
{
    /**
     * @var integer The maximum depth level in the object tree.
     */
    protected $_maxLevel = 25;

    /**
     * @var integer The current depth level in the object tree.
     */
    protected $_currentLevel = 0;

    /**
     * @var array<mixed> A storage for objects that already have been processed.
     */
    protected $_storage = [];

    /**
     * @var string The output.
     */
    private $_output = '';

    /**
     * Defines if the output should be passed through htmlspecialchars()
     *
     * @var boolean
     */
    protected $_isWebEnvironment = -1;

    /**
     * The output shown when the maximum (depth) level is reached.
     */
    const MAX_LEVEL_REACHED_MESSAGE = '(ML)';


    /**
     * The constructor takes the object to debug as it's argument
     *
     * @param mixed $object
     * @return    \Iresults\Core\Debug
     */
    public function __construct($object = null)
    {
        /*
         * @Info: The object argument must be optional because Flow throws an
         * exception if isset(arguments[0]) evaluates to FALSE.
         */
        if (func_num_args() > 0) {
            $this->debug($object);
        }

        return $this;
    }

    /**
     * Debugs a variable
     *
     * @param mixed   $object               The object/variable to debug
     * @param boolean $tempIsWebEnvironment Temporarily overwrite the isWebEnvironment configuration
     * @return    $this
     */
    public function debug($object, $tempIsWebEnvironment = -1)
    {
        $this->_storage = [];
        $this->_output = '';
        $this->_debug($object, $tempIsWebEnvironment);

        unset($this->_storage);
        $this->_storage = null;

        return $this;
    }

    /**
     * Debugs a variable
     *
     * @param mixed   $object               The object/variable to debug
     * @param boolean $tempIsWebEnvironment Temporarily overwrite the isWebEnvironment configuration
     * @return    $this
     */
    protected function _debug($object, $tempIsWebEnvironment = -1)
    {
        $objectId = '';
        $oldIsWebEnvironment = -1;
        static $printHash = true;

        // Check if the deepest level is reached.
        if ($this->_currentLevel >= $this->_maxLevel) {
            $this->_add(self::MAX_LEVEL_REACHED_MESSAGE);

            return $this;
        }
        $this->_currentLevel = $this->_currentLevel + 1;


        /*
         * Check the storage if the object does already exist
         */
        if (is_object($object)) {
            $hash = spl_object_hash($object);

            if (isset($this->_storage[$hash])) {
                $this->_add('Recursion for object ' . get_class($object) . ' #' . $hash);
                $this->_currentLevel = $this->_currentLevel - 1;

                return $this;
            }

            $this->_storage[$hash] = $object;
            if ($printHash) {
                $objectId = '#' . $hash . ' ';
            }
        }

        // Check the environment
        if ($this->_isWebEnvironment === -1) {
            $this->getIsWebEnvironment();
        }
        if ($tempIsWebEnvironment !== -1) {
            $oldIsWebEnvironment = $this->_isWebEnvironment;
            $this->_isWebEnvironment = $tempIsWebEnvironment;
        }

        /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        if (is_null($object)) {
            $this->_add('NULL');
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (is_bool($object)) {
            $msg = '(bool) TRUE';
            if (!$object) {
                $msg = '(bool) FALSE';
            }
            $this->_add($msg);
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (is_string($object)) {
            if ($this->_isWebEnvironment) {
                $this->_add('(string) "' . htmlspecialchars($object) . '"');
            } else {
                $this->_add('(string) "' . $object . '"');
            }

            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (is_int($object)) {
            $this->_add("(int) $object");
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (is_float($object)) {
            $this->_add("(float) $object");
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (is_array($object)) {
            $this->_add('array (' . count($object) . ') => {');
            $this->_currentLevel = $this->_currentLevel + 1;
            foreach ($object as $key => $element) {
                $this->_add("$key => ");
                $this->_debug($element);
            }
            $this->_currentLevel = $this->_currentLevel - 1;
            $this->_add('}');
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (get_class($object) == '\Iresults\Core\Nil') {
            $this->_add('nil');
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif ($object instanceof \Traversable) {
            $this->_add(get_class($object) . ' (' . $objectId . count($object) . ') => {');
            $this->_currentLevel = $this->_currentLevel + 1;
            foreach ($object as $key => $element) {
                $this->_add("$key => ", false);
                $this->_debug($element);
            }
            $this->_currentLevel = $this->_currentLevel - 1;
            $this->_add('}');
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (method_exists($object, '_ir_debug') && is_callable([$object, '_ir_debug'])) {
            $properties = $object->_ir_debug();
            $this->_add(get_class($object) . $objectId . ' => {');
            $this->_currentLevel = $this->_currentLevel + 1;
            foreach ($properties as $key => $element) {
                $this->_add("$key => ", false);
                $this->_debug($element);
            }
            $this->_currentLevel = $this->_currentLevel - 1;
            $this->_add('}');
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif ($object instanceof \DateTime) {
            $dateTime = get_class($object) . ' => {' . $object->format('r') . '}';
            $this->_add($dateTime);
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif ($object instanceof \Iresults\Core\DateTime) {
            $dateTime = var_export($object, true);
            $dateTime = str_replace('::__set_state(array(', ' => {', $dateTime);
            $dateTime = str_replace('))', '}', $dateTime);
            $dateTime = str_replace([",\n", ",\r", ",\r\n"], PHP_EOL, $dateTime);
            $this->_add($dateTime);
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (is_a($object, 'Tx_Extbase_Error_Message')) {
            $this->_add(get_class($object) . " => $object");
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (method_exists($object, '_getProperties')) {
            $properties = $object->_getProperties();
            $this->_add(get_class($object) . $objectId . ' => {');
            $this->_currentLevel = $this->_currentLevel + 1;
            foreach ($properties as $key => $element) {
                $this->_add("$key => ", false);
                $this->_debug($element);
            }
            $this->_currentLevel = $this->_currentLevel - 1;
            $this->_add('}');
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (is_a($object, 'tslib_cObj')) {
            $properties = $object->data;
            $this->_add(get_class($object) . $objectId . ' => {');
            $this->_currentLevel = $this->_currentLevel + 1;
            foreach ($properties as $key => $element) {
                $this->_add("$key => ", false);
                $this->_debug($element);
            }
            $this->_currentLevel = $this->_currentLevel - 1;
            $this->_add('}');
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (method_exists($object, 'getData')) {
            $properties = $object->getData();
            $this->_add(get_class($object) . $objectId . ' => {');
            $this->_currentLevel = $this->_currentLevel + 1;
            foreach ($properties as $key => $element) {
                $this->_add("$key => ", false);
                $this->_debug($element);
            }
            $this->_currentLevel = $this->_currentLevel - 1;
            $this->_add('}');
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (is_a($object, '\Iresults\Core\Value')) {
            $property = $object->getValue();
            $this->_add(get_class($object) . $objectId . ' => {');
            $this->_currentLevel = $this->_currentLevel + 1;
            $this->_debug($property);
            $this->_currentLevel = $this->_currentLevel - 1;
            $this->_add('}');
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (is_a($object, '\Iresults\FS\FilesystemInterface')) {
            $this->_add(get_class($object) . $objectId . ' => {');
            $this->_currentLevel = $this->_currentLevel + 1;
            $this->_add('path => "' . $object->getPath() . '"');
            $this->_currentLevel = $this->_currentLevel - 1;
            $this->_add('}');
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif ($object instanceof \Exception) {
            $this->_add(get_class($object) . $objectId . ' => {');
            $this->_currentLevel = $this->_currentLevel + 1;
            $this->_add($object->getCode() . ': ' . $object->getMessage());
            $this->_currentLevel = $this->_currentLevel - 1;
            $this->_add('}');
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (is_resource($object)) {
            $this->_add('(resource) ' . get_resource_type($object) . ' ' . substr('' . $object, 9));
            /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
        } elseif (is_object($object)) {
            $properties = get_object_vars($object);
            $this->_add(get_class($object) . $objectId . ' => {');
            $this->_currentLevel = $this->_currentLevel + 1;
            foreach ($properties as $key => $element) {
                $this->_add("$key => ", false);
                $this->_debug($element);
            }
            $this->_currentLevel = $this->_currentLevel - 1;
            $this->_add('}');
        }

        $this->_currentLevel = $this->_currentLevel - 1;
        if ($tempIsWebEnvironment !== -1) {
            $this->_isWebEnvironment = $oldIsWebEnvironment;
        }

        return $this;
    }

    /**
     * Adds a text to the output
     *
     * @param string $text
     * @return    void
     */
    protected function _add($text)
    {
        // Check the environment and print &nbsp; for web and \t for a shell
        if ($this->_isWebEnvironment === -1) {
            $this->getIsWebEnvironment();
        }
        for ($i = 1; $i < $this->_currentLevel; $i++) {
            if ($this->_isWebEnvironment) {
                $this->_output .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            } else {
                //$this->_output .= "\t";
                $this->_output .= '   ';
            }
        }
        $this->_output .= "$text" . PHP_EOL;
    }

    /**
     * Returns the output
     *
     * @return    string
     */
    protected function _get()
    {
        return $this->_output;
    }

    /**
     * Returns the debug output
     *
     * @return    string
     */
    public function get()
    {
        return $this->_output;
    }

    public function __toString()
    {
        return $this->_get();
    }

    /**
     * Returns the environment setting
     *
     * @return boolean
     */
    public function getIsWebEnvironment()
    {
        if ($this->_isWebEnvironment === -1) {
            $this->_isWebEnvironment = (
                Iresults::getEnvironment() === Iresults::ENVIRONMENT_WEB
                && Iresults::getOutputFormat() === Iresults::OUTPUT_FORMAT_XML
            );
        }

        return $this->_isWebEnvironment;
    }

    /**
     * Overwrite the environment setting
     *
     * @param boolean $isWebEnvironment
     * @return boolean                        Returns the original value
     */
    public function setIsWebEnvironment($isWebEnvironment)
    {
        $oldIsWebEnvironment = $this->_isWebEnvironment;
        $this->_isWebEnvironment = $isWebEnvironment;

        return $oldIsWebEnvironment;
    }

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns the shared instance.
     *
     * @return \Iresults\Core\Debug
     */
    static public function sharedInstance()
    {
        static $sharedInstance = null;
        if (!$sharedInstance) {
            $sharedInstance = new self();
        }

        return $sharedInstance;
    }
}
