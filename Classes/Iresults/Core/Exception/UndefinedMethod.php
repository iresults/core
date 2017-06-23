<?php

namespace Iresults\Core\Exception;


/**
 * The iresults Undefined Method exception.
 *
 * = Examples =
 * <code>
 * throw \Iresults\Core\Exception\UndefinedMethod::exceptionWithMethod('theUndefinedMethod', array('user information'));
 * </code>
 */
class UndefinedMethod extends \Iresults\Core\Error
{
    /**
     * Exception message
     */
    const MESSAGE = 'Call to undefined method %s';

    /**
     * Constructs the Exception.
     *
     * @param string     $message  The Exception message to throw
     * @param integer    $code     The Exception code
     * @param \Exception $previous The previous exception used for the exception chaining
     */
    public function __construct($message = '', $code = 0, $previous = null)
    {
        if ($code === 0) {
            $code = 1346336887;
        }
        parent::__construct($message, $code, $previous);
    }



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Factory method: Returns a new UndefinedMethod exception with the given
     * method name in the message.
     *
     * @param string $method   The name of the undefined method
     * @param array  $userInfo An optional user info array
     * @return \Iresults\Core\Exception\UndefinedMethod
     */
    static public function exceptionWithMethod($method, $userInfo = [])
    {
        $message = sprintf(self::MESSAGE, $method);
        $error = new static($message);
        $error->_setUserInfo($userInfo);

        return $error;
    }
}
