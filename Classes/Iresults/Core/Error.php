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


/**
 * The iresults error class extends a PHP exception with an additional user info
 * property to allow more information to be transported with the error.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults
 */
class Error extends \Exception implements JsonSerializable
{
    /**
     * The additional information transported with this error.
     *
     * @var array<mixed>
     */
    protected $userInfo = array();

    /**
     * Gets the user info dictionary.
     *
     * @return    array<mixed>
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Setter for userInfo
     *
     * @param    array <mixed> $newValue The new value to set
     * @return    void
     * @internal
     */
    public function _setUserInfo($newValue)
    {
        $this->userInfo = $newValue;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array Returns data which can be serialized by json_encode(), which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return array(
            'code'     => $this->code,
            'message'  => $this->message,
            'userInfo' => $this->userInfo,
        );
    }

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Factory method: Returns a new error with the given message, code and
     * user information
     *
     * @param string $message  Exception message
     * @param int    $code     Exception code
     * @param array  $userInfo User info array
     * @return \Iresults\Core\Error
     */
    static public function errorWithMessageCodeAndUserInfo($message, $code = 0, $userInfo = array())
    {
        /** @var Error $error */
        $error = new static($message, $code);
        $error->_setUserInfo($userInfo);

        return $error;
    }
}

if (!class_exists('Error')) {
    class_alias('\Iresults\Core\Error', 'Error');
}
