<?php
namespace Iresults\Core\Helpers;

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
     * @param    object $object       The object to get the value from
     * @param    string $propertyKey  The name of the property
     * @param    string $getterMethod The name of the method to be used
     * @return    mixed
     */
    protected function _invokeGetterMethodOnObject($object, $propertyKey, $getterMethod = null)
    {
        if ($getterMethod === null) {
            return ObjectHelper::getObjectForKeyPathOfObject($propertyKey, $object);
        }

        //\Iresults\Core\Iresults::pd($propertyKey,$getterMethod,$object,call_user_func_array(array($object,$getterMethod), array($propertyKey)));

        if (method_exists($object, $getterMethod)) {
            return call_user_func_array(array($object, $getterMethod), array($propertyKey));
        } else {
            return null;
        }
    }
}
