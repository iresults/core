<?php
/*
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * @author COD
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
    protected $configuration = array();

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