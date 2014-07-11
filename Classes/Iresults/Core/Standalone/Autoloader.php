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
 * Created 03.10.13 10:08
 */


namespace Iresults\Core\Standalone;

/**
 * Autoloader for Iresults core classes
 *
 * @tutorial
 * require_once 'Classes/Iresults/Core/Standalone/Autoloader.php';
 * \Iresults\Core\Standalone\Autoloader::register();
 */
class Autoloader {
	/**
	 * Defines if the Autoloader has been registered
	 * @var bool
	 */
	static protected $isRegistered = FALSE;

    /**
     * Registers the class to use for autoloading
	 *
	 * @param bool $prepend If TRUE, the Autoloader will be prepended on the autoload stack instead of being appended
	 */
	static public function register($prepend = FALSE) {
		if (self::$isRegistered) {
			return;
		}
		self::$isRegistered = spl_autoload_register(array(__CLASS__, 'autoload'), TRUE, $prepend);
    }

    /**
     * Unregisters the class to use for autoloading
     */
    static public function unregister() {
        spl_autoload_unregister(array(__CLASS__, 'autoload'));
    }

    /**
     * Tries to autoload the given class
     *
     * @param string $className
     */
    static public function autoload($className) {
        if ($className[0] !== '\\') {
            $className = '\\' . $className;
        }
        if (substr($className, 0, 15) !== '\\Iresults\\Core\\') {
            return;
        }
        $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
        require_once self::getClassBasePath() . $classFile;
    }

    /**
     * Returns the base path of the Iresults core files
     *
     * @return string
     */
    static protected function getClassBasePath() {
        static $classBasePath = '';
        if (!$classBasePath) {
            $classBasePath = __DIR__ . '/../../../';
        }
        return $classBasePath;
    }
}
