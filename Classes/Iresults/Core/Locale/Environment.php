<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 03.10.13 17:51
 */


namespace Iresults\Core\Locale;

use Iresults\Core\Iresults;
use Iresults\Core\Locale\Exception\LocaleException;

/**
 * Manager for the locale environment
 *
 * @package Iresults\Core\Locale
 */
class Environment
{
    /**
     * The current locale
     *
     * @var string
     */
    protected $locale;

    /**
     * Shared instance
     *
     * @var Environment
     */
    static protected $sharedInstance;

    /**
     *
     */
    function __construct()
    {
        $locale = setlocale(LC_CTYPE, '0');
        if ($locale === 'C') {
            $locale = Iresults::getLocale();
        }
        if ($locale === 'UTF-8') {
            $locale = Iresults::getLocale() . '.UTF-8';
        }
        $this->setLocale($locale);
    }


    /**
     * Sets the current locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        if (setlocale(LC_ALL, $locale) === false) {
            throw new LocaleException(sprintf('Locale "%s" not found', $locale), 1433151580);
        }
        $this->locale = $locale;
        putenv('LC_ALL=' . $locale);
    }

    /**
     * Returns the current locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Invokes the given callable with the locale temporary set
     *
     * If an array is given as callable it has to be in one of the following
     * formats:
     *
     *    array(
     *        'functionName',
     *        array($arg0, $arg1, ... $argN) // Optional
     *    )
     *
     *    array(
     *        array($object, 'methodName'),
     *        array($arg0, $arg1, ... $argN) // Optional
     *    )
     *
     * The callable array will be invoked through call_
     *
     * @param string         $locale   The temporary locale to use
     * @param \Closure|array $callable The callable code
     * @return mixed Returns the invocations result
     */
    public function executeWithLocale($locale, $callable)
    {
        // Set the temporary locale
        $oldLocale = $this->getLocale();
        $this->setLocale($locale);

        // Check if the callable is an array
        if (is_array($callable)) {
            if (count($callable) > 1) {
                $result = call_user_func_array($callable[0], $callable[1]);
            } else {
                $result = call_user_func($callable[0]);
            }
        } else {
            $result = $callable();
        }

        // Reset the locale
        $this->setLocale($oldLocale);

        return $result;
    }

    /**
     * Returns the shared instance
     *
     * @return Environment
     */
    static public function getSharedInstance()
    {
        if (!static::$sharedInstance) {
            static::$sharedInstance = new static();
        }

        return static::$sharedInstance;
    }
}