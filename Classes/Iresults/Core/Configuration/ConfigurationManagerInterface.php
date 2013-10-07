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
 * Created 07.10.13 15:34
 */


namespace Iresults\Core\Configuration;

/**
 * A common interface for configuration providers
 *
 * @package Iresults\Core\Configuration
 */
interface ConfigurationManagerInterface {
	/**
	 * Returns the configuration for the given key path
	 *
	 * @param string $keyPath Key path of the configuration
	 * @param string $package Optional package key to retrieve the configuration for a specific package
	 * @return mixed
	 */
	public function getConfigurationForKeyPath($keyPath, $package = NULL);

	/**
	 * Sets the configuration at the given key path
	 *
	 * @param string $keyPath Key path of the configuration
	 * @param mixed  $value   Configuration
	 * @param string $package Optional package key to overwrite the configuration for a specific package
	 * @return $this
	 */
	public function setConfigurationForKeyPath($keyPath, $value, $package = NULL);
}