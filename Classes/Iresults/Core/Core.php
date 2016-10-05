<?php
namespace Iresults\Core;

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

use Iresults\Core\Exception\UndefinedMethod;


/**
 * Base class for iresults classes.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Core
 * @version       1.2.0
 */
abstract class Core implements \Iresults\Core\ObjectInterface
{
    /**
     * The delegate of this renderer.
     *
     * @var object
     */
    protected $_delegate = null;

    const IR_METHOD_NOT_FOUND = '__ir_method_not_found_';

    const DEBUG_LEVEL_INFO = -1;
    const DEBUG_LEVEL_WARNING = 1;
    const DEBUG_LEVEL_CRITICAL = 2;

    const DEBUG_PRINT_LEVEL_OFF = 0;
    const DEBUG_PRINT_LEVEL_ALL = 1;
    const DEBUG_PRINT_LEVEL_CRITICAL = 2;

    static private $_debugLevel = 2;

    /**
     * Tries to invoke a dynamically added method.
     *
     * @param    string $name      The originally called method
     * @param    array  $arguments The arguments passed to the original method
     * @return    mixed
     * @throws    UndefinedMethod    If no dynamic method was found
     */
    public function __call($name, array $arguments)
    {
        if (isset($this->$name) && is_callable($this->$name)) {
            $dynamicMethod = $this->$name;

            return call_user_func_array($dynamicMethod, $arguments);
        }
        if (($method = static::_instanceMethodForSelector($name))) {
            // Add the current object to the array of arguments
            array_unshift($arguments, $this);

            return call_user_func_array($method, $arguments);
        }
        throw UndefinedMethod::exceptionWithMethod($name);
    }

    /**
     * Triggered when invoking inaccessible methods in a static context.
     *
     * @param    string $name      The name of the called function
     * @param    array  $arguments An enumerated array containing the parameters passed to the inaccessible method
     * @return    mixed    The return value
     *
     * @throws UndefinedMethod    If no dynamic method was found
     */
    static public function __callStatic($name, array $arguments)
    {
        throw UndefinedMethod::exceptionWithMethod($name);
    }

    /**
     * Returns or sets the callback for the given method name
     *
     * Advanced information: Since the $categoryMethods is defined static (in
     * the local scope) it is not shared among subclasses. Say you add function
     * a() to class A. The subclass B has no access to function a().
     * But there seems to be a way to inherit dynamic functions if the class B
     * is compiled after function a() has been added to class A. Please be
     * aware that you should not rely on this behaviour!
     *
     * @param  string   $methodName
     * @param  callback $callback
     * @return callback    Returns the method for the given name or FALSE
     */
    static public function _instanceMethodForSelector($methodName, $callback = null)
    {
        /**
         * An array of callbacks (i.e. closures)
         *
         * @var array
         */
        static $categoryMethods = array();

        if ($callback) {
            $categoryMethods[$methodName] = $callback;

            return $callback;
        } elseif (isset($categoryMethods[$methodName])) {
            return $categoryMethods[$methodName];
        }

        return false;
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Factory method: Returns a new instance of the class.
     *
     * @return    \Iresults\Core\Core
     */
    static public function alloc()
    {
        return new static();
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* DEBUGGING         MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Dumps a given variable (or the given variables) wrapped into a 'pre' tag.
     *
     * @var mixed $var1
     * @return string
     */
    public function pd($var1 = '__iresults_pd_noValue')
    {
        $args = func_get_args();

        return call_user_func_array(array('\Iresults\Core\Iresults', 'pd'), $args);
    }

    /**
     * Prints a given message if the debug level is set to DEBUG_PRINT_LEVEL_ALL.
     *
     * @param    string|mixed $msg
     */
    public function debug($msg, $level = -1)
    {
        if ($level == -1) {
            $level = self::DEBUG_LEVEL_INFO;
        }

        if (self::$_debugLevel == self::DEBUG_PRINT_LEVEL_ALL OR
            (self::$_debugLevel == self::DEBUG_PRINT_LEVEL_CRITICAL AND $level == self::DEBUG_LEVEL_CRITICAL)
        ) {
            echo "<div class='iresults_debug'>$msg</div>";
        }
    }

    /**
     * The magic __toString function
     *
     * @return    string
     */
    public function __toString()
    {
        return $this->description();
    }

    /**
     * Returns the default description.
     *
     * @return    string    A string describing this object
     */
    public function description()
    {
        return Iresults::descriptionOfValue($this);
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* DELEGATION        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Tries to call a method on the delegate or, if the delegate doesn't respond,
     * the method will be tried on $this.
     *
     * @param    string $method    The name of the method to invoke
     * @param    array  $arguments Optional arguments to pass to the object
     * @param    object $object    Optional object to be checked first
     * @return    mixed|Core::IR_METHOD_NOT_FOUND
     * @throws \InvalidArgumentException if the arguments are not of type array
     */
    protected function _callMethodIfExists($method, $arguments = array(), $object = null)
    {
        if ($object !== null && is_object($object)) {
            if (method_exists($object, $method)) {
                $arguments[] = $this;

                return call_user_func_array(array($object, $method), $arguments);
            }
        }
        if ($this->_delegate && is_object($this->_delegate) && method_exists($this->_delegate, $method)) {
            if (!is_array($arguments)) {
                throw new \InvalidArgumentException(
                    'Passed delegation argument is not an array. It is of type ' . gettype($arguments)
                );
            }
            $arguments[] = $this;

            return call_user_func_array(array($this->_delegate, $method), $arguments);
        }
        if (method_exists($this, $method)) {
            $arguments[] = $this;

            return call_user_func_array(array($this, $method), $arguments);
        }

        return self::IR_METHOD_NOT_FOUND;
    }
}
