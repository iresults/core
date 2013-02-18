<?php
namespace Iresults\Core\Mutable;

/*
 * Copyright notice
 *
 * (c) 2010 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *             Daniel Corn <cod@iresults.li>, iresultsaniel Corn <cod@iresults.li>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */



/**
 * The concrete implementation class for mutable objects that read data from YAML
 * files using Zend_Config_Yaml.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Mutable
 */
class Yaml extends \Iresults\Core\Mutable {


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Factory method: Returns a mutable object representing the data from the
	 * given URL.
	 *
	 * @param	string	$url URL of the file to read
	 * @return	\Iresults\Core\Mutable
	 */
	static public function mutableWithContentsOfUrl($url) {
		$mutable = NULL;
		if (class_exists('\Symfony\Component\Yaml\Yaml')) { // Symfony YAML
			$mutable = new \Iresults\Core\Mutable\Yaml\SymfonyYaml();
			$mutable->initWithContentsOfUrl($url);
		} else {
			throw new \Exception('No concrete implementation for "\Iresults\Core\Mutable\Yaml" found', 1320674794);
		}
		return $mutable;
	}
}