<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 22.10.13 14:27
 */


namespace Iresults\Core\Parser;

use Iresults\Core\Core;

/**
 * Abstract parser class
 *
 * @package Iresults\Core\Parser
 */
abstract class AbstractParser extends Core implements ParserInterface
{
    /**
     * Parser configuration
     *
     * @var array
     */
    protected $configuration = [];

    /**
     * Set the configuration array
     *
     * @param array $configuration
     * @return $this
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Returns the configuration array
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns the configuration for the given key
     *
     * @param string $key Key of the configuration value
     * @return mixed
     */
    public function getConfigurationForKey($key)
    {
        if (isset($this->configuration[$key])) {
            return $this->configuration[$key];
        }

        return null;
    }
}