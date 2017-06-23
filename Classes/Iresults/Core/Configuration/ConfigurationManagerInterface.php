<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 07.10.13 15:34
 */


namespace Iresults\Core\Configuration;

/**
 * A common interface for configuration providers
 *
 * @package Iresults\Core\Configuration
 */
interface ConfigurationManagerInterface
{
    /**
     * Returns the configuration for the given key path
     *
     * @param string $keyPath Key path of the configuration
     * @param string $package Optional package key to retrieve the configuration for a specific package
     * @return mixed
     */
    public function getConfigurationForKeyPath($keyPath, $package = null);

    /**
     * Sets the configuration at the given key path
     *
     * @param string $keyPath Key path of the configuration
     * @param mixed  $value   Configuration
     * @param string $package Optional package key to overwrite the configuration for a specific package
     * @return $this
     */
    public function setConfigurationForKeyPath($keyPath, $value, $package = null);
}