<?php

namespace Iresults\Core\Helpers;


/**
 * The iresults sort helper provides different methods to sort arrays of
 * objects. It also enables you to detect and retrieve subgroups of object.
 * Subgroups are arrays of objects that share the same property value for a
 * given property key.
 *
 * @package    Iresults
 * @subpackage Helpers
 * @version    1.5
 */
class SortHelper extends SortHelperAbstract
{
    /**
     * Invokes the getter method on the given object. If no getter method is specified
     * or the given value is NULL _getProperty() will be called.
     *
     * @param object $object       The object to get the value from
     * @param string $propertyKey  The name of the property
     * @param string $getterMethod The name of the method to be used
     * @return    mixed
     */
    protected function _invokeGetterMethodOnObject($object, $propertyKey, $getterMethod = null)
    {
        if ($getterMethod === null) {
            return ObjectHelper::getObjectForKeyPathOfObject($propertyKey, $object);
        }

        //\Iresults\Core\Iresults::pd($propertyKey,$getterMethod,$object,call_user_func_array(array($object,$getterMethod), array($propertyKey)));

        if (method_exists($object, $getterMethod)) {
            return call_user_func_array([$object, $getterMethod], [$propertyKey]);
        } else {
            return null;
        }
    }
}
