<?php

namespace Iresults\Core\Helpers;


/**
 * The serializer provides functions to transform objects into arrays, or other
 * data types.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Helpers
 */
class SerializationHelper extends \Iresults\Core\Core
{
    /**
     * Key for additional configurations to transform object storages into
     * indexed arrays instead of dictionaries.
     */
    const CONFIGURATION_KEY_OBJECTSTORAGE_TO_ARRAY = 'configuration_key/objectstorage_to_array';

    /**
     * Indicates if object storages should be conferted into indexed arrays
     * instead of dictionaries.
     *
     * @var boolean
     */
    static protected $transformObjectStorageToArray = true;

    /**
     * An array as cache for already converted keys.
     *
     * @var array<string>
     */
    static protected $updatedKeys = [];

    #static protected $treatObjectStorageAsArray = FALSE;

    /**
     * Returns a plain dictionary of the given dictionary or object.
     *
     * In contrast to the levelOut() method each accessible property will be
     * traversed. To get the properties either _getProperties(), or getData() is
     * called.
     *
     * @param array|object $input                    The input to traverse
     * @param integer      $convertKeys              Configure the way the keys are converted
     * @param array        $additionalConfigurations Additional configurations to apply
     * @return    array<mixed>
     */
    static public function objectToArray(
        $input,
        $convertKeys = \Iresults\Core\Tools\StringTool::FORMAT_KEEP,
        $additionalConfigurations = []
    ) {
        $result = [];
        $updatedKeyPrefix = '';

        /*
         * Apply additional configuration.
         */
        if (!empty($additionalConfigurations)) {
            if (isset($additionalConfigurations[self::CONFIGURATION_KEY_OBJECTSTORAGE_TO_ARRAY])) {
                self::$transformObjectStorageToArray = $additionalConfigurations[self::CONFIGURATION_KEY_OBJECTSTORAGE_TO_ARRAY];
            }
        }

        /*
         * If the input is an object try to get it's properties.
         */
        if (is_object($input)) {
            if (!$input instanceof \Traversable) { // If it isn't a traversable
                $updatedKeyPrefix = ObjectHelper::createIdentfierForObject($input);

                if (defined('TYPO3_MODE') && method_exists($input, '_getProperties')) {
                    $input = $input->_getProperties();
                } elseif (is_a($input, '\Iresults\Core\Mutable') || method_exists($input, 'getData')) {
                    $input = $input->getData();
                } elseif (method_exists($input, 'getUid')) {
                    return $input->getUid();
                }
            } elseif (self::$transformObjectStorageToArray && (
                    is_a($input, 'SplObjectStorage') ||
                    is_a($input, 'Tx_Extbase_Persistence_ObjectMonitoringInterface')
                )
            ) { // If it is an object storage and $transformObjectStorageToArray is TRUE
                $input = iterator_to_array($input, false);
            }
        } elseif (!is_array($input)) {
            return [];
        }

        /*
         * Loop through each property.
         */
        foreach ($input as $name => $value) {
            if ($updatedKeyPrefix && in_array($updatedKeyPrefix . $name, self::$updatedKeys, true)) {
                self::_debug("Key '$name' has already been updated (prefix: $updatedKeyPrefix).");
                continue;
            } elseif ($updatedKeyPrefix) {
                #self::_debug("Key '$name' was updated (prefix: $updatedKeyPrefix).");
            }

            $newValue = null;
            /*
             * Check if null
             */
            if (is_null($value)) {
                #self::_debug("Value for $name is NULL.");
                continue;
            } else /*
			 * Is scalar use the original value
			 */ {
                if (is_scalar($value)) {
                    $newValue = $value;
                } else /*
			 * Is \Iresults\Core\DateTime or DateTime use the unix timestamp
			 */ {
                    if (is_a($value, '\Iresults\Core\DateTime') || is_a($value, 'DateTime')) {
                        #self::_debug("Value for $name is a \Iresults\Core\DateTime (".get_class($value).").");
                        $newValue = $value->format('U');
                    } else /*
			 * Is \Iresults\Core\Value use the value
			 */ {
                        if (is_a(
                            $value,
                            '\Iresults\Core\Value'
                        )) { // If it is a iresults Value object return the value.
                            $newValue = $value->getValue();
                        } else /*
			 * Is another object, or an array
			 */ {
                            if (is_object($value) || is_array($value)) {
                                #self::_debug("Value for $name is an object with a getUid method (".get_class($value).").");
                                $newValue = self::objectToArray($value, $convertKeys);
                            } else /*
			 * Use the original value
			 */ {
                                #self::_debug("Value for $name is a simple value (".gettype($value).").");
                                $newValue = $value;
                            }
                        }
                    }
                }
            }

            /*
             * Convert the keys according to the convertKeys argument.
             */
            if ($convertKeys !== \Iresults\Core\Tools\StringTool::FORMAT_KEEP) {
                $name = \Iresults\Core\Tools\StringTool::transformStringToFormat($name, $convertKeys);
            }


            $result[$name] = $newValue;

            /**
             * @todo Is this correct?
             */
            self::$updatedKeys[] = $name;
        }

        /**
         * @todo Is this correct?
         */
        self::$updatedKeys = [];

        return $result;
    }

    /**
     * Set if object storages should be conferted into indexed arrays
     * instead of dictionaries.
     *
     * @param boolean $flag
     * @return    void
     */
    public function setTransformObjectStorageToArray($flag)
    {
        self::$transformObjectStorageToArray = $flag;
    }

    /**
     * Returns if object storages should be conferted into indexed arrays
     * instead of dictionaries.
     *
     * @return    boolean
     */
    public function getTransformObjectStorageToArray()
    {
        return self::$transformObjectStorageToArray;
    }

    /**
     * Prints a message if self:$debug is set to TRUE.
     *
     * @param string|object $msg The message or object to output
     *
     * @return    void
     */
    static protected function _debug($msg)
    {
        \Iresults\Core\Iresults::pd($msg);
    }
}
