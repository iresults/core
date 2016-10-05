<?php
namespace Iresults\Core\Helpers\Object;

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
     * @param    Mutable $mutable   The mutable whose properties to transform
     * @param    boolean $recursive Set to TRUE if you want the transformation recursively
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
     * @param    Mutable $mutable The mutable whose properties to transform
     * @return    void
     */
    static public function transformPropertiesOfMutableToObjectsRecursive($mutable)
    {
        self::transformPropertiesOfMutableToObjects($mutable, true);
    }
}
