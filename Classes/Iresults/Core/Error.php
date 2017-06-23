<?php

namespace Iresults\Core;


/**
 * The iresults error class extends a PHP exception with an additional user info
 * property to allow more information to be transported with the error.
 */
class Error extends \Exception implements \JsonSerializable
{
    /**
     * The additional information transported with this error.
     *
     * @var array<mixed>
     */
    protected $userInfo = [];

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
     * @param array <mixed> $newValue The new value to set
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
        return [
            'code'     => $this->code,
            'message'  => $this->message,
            'userInfo' => $this->userInfo,
        ];
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
    static public function errorWithMessageCodeAndUserInfo($message, $code = 0, $userInfo = [])
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
