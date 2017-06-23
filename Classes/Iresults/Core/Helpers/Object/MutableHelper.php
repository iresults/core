<?php

namespace Iresults\Core\Helpers\Object;

use Iresults\Core\Helpers\ObjectHelper;
use Iresults\Core\Mutable;


/**
 * The iresults object Mutable Helper provides functions to transform mutable
 * objects.
 *
 * @package    Iresults
 * @subpackage Iresults_Helpers_Object
 * @version    1.5
 */
class MutableHelper extends \Iresults\Core\Core
{
    /**
     * Transforms each property of the given mutable into an object.
     *
     * @param Mutable $mutable   The mutable whose properties to transform
     * @param boolean $recursive Set to TRUE if you want the transformation recursively
     * @return    void
     */
    static public function transformPropertiesOfMutableToObjects($mutable, $recursive = false)
    {
        $mutableData = null;
        if (is_array($mutable)) {
            $mutableData = $mutable;
        } else {
            $mutableData = $mutable->getData();
        }

        foreach ($mutableData as $key => $value) {
            $newValue = null;

            /*
             * If value isn't an object, try to create a new object of the best
             * kind.
             */
            if (!is_object($value)) {
                if (is_resource($value)) {
                    throw new \InvalidArgumentException("Cannot transform a resource into an object.", 1321634463);
                }
                $newValue = ObjectHelper::createObjectWithValue($value);
                if ($recursive && $newValue instanceof Mutable) {
                    self::transformPropertiesOfMutableToObjects($newValue, true);
                }
                $mutable->setObjectForKey($key, $newValue);
            }
        }
    }

    /**
     * Transforms each property of the given mutable into an object recursively.
     *
     * @param Mutable $mutable The mutable whose properties to transform
     * @return    void
     */
    static public function transformPropertiesOfMutableToObjectsRecursive($mutable)
    {
        self::transformPropertiesOfMutableToObjects($mutable, true);
    }
}
