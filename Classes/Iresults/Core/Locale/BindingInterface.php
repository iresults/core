<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 07.10.13 13:21
 */


namespace Iresults\Core\Locale;

/**
 * Defines a common interface for class instances that can be bound to a specific
 * locale
 *
 * @package Iresults\Core\Locale
 */
interface BindingInterface
{
    /**
     * Binds the instance to the given locale
     *
     * @param string $locale
     * @return $this
     */
    public function bindToLocale($locale);

    /**
     * Returns the locale this instance is bound to
     *
     * @return string
     */
    public function getBoundLocale();
}