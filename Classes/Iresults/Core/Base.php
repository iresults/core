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
 *         Created 02.10.13 16:24
 */


namespace Iresults\Core;

/**
 * Base class of the iresults framework
 *
 * One implementation of the shared instance for \Iresults\Core\Iresults
 */
class Base extends AbstractBase {
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

	########     ###    ######## ##     ##    ########  ########  ######   #######  ##       ##     ## ######## ####  #######  ##    ##
	##     ##   ## ##      ##    ##     ##    ##     ## ##       ##    ## ##     ## ##       ##     ##    ##     ##  ##     ## ###   ##
	##     ##  ##   ##     ##    ##     ##    ##     ## ##       ##       ##     ## ##       ##     ##    ##     ##  ##     ## ####  ##
	########  ##     ##    ##    #########    ########  ######    ######  ##     ## ##       ##     ##    ##     ##  ##     ## ## ## ##
	##        #########    ##    ##     ##    ##   ##   ##             ## ##     ## ##       ##     ##    ##     ##  ##     ## ##  ####
	##        ##     ##    ##    ##     ##    ##    ##  ##       ##    ## ##     ## ##       ##     ##    ##     ##  ##     ## ##   ###
	##        ##     ##    ##    ##     ##    ##     ## ########  ######   #######  ########  #######     ##    ####  #######  ##    ##

	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	/**
	 * Returns the path to the base directory of the installation.
	 *
	 * @return    string
	 */
	public function getBasePath() {
		if (!self::$basePath) {
			if (defined('PATH_site')) {
				self::$basePath = PATH_site;
			} else if (defined('FLOW_PATH_ROOT')) {
				self::$basePath = FLOW_PATH_ROOT;
			} else if (realpath($_SERVER['SCRIPT_FILENAME']) !== FALSE) {
				self::$basePath = realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/';
			} else {
				self::$basePath = dirname(__FILE__) . '/../../../';
			}
		}
		return self::$basePath;
	}

	/**
	 * Returns the base URL of the index.php file.
	 *
	 * @return    string
	 */
	public function getBaseURL() {
		if (!self::$baseURL) {
			$tempBaseURL = '';
			if (defined('TYPO3_MODE') && $GLOBALS['TSFE']->baseUrl) {
				$tempBaseURL = $GLOBALS['TSFE']->baseUrl;
			} else {
				$tempBaseURL = dirname($_SERVER['SCRIPT_NAME']);
			}

			if (substr($tempBaseURL, -1) !== '/') {
				$tempBaseURL .= '/';
			}
			self::$baseURL = $tempBaseURL;
		}
		return self::$baseURL;
	}

	/**
	 * Returns the path to the temporary directory.
	 *
	 * @return    string
	 */
	public function getTempPath() {
		static $path = '';
		if (!$path) {
			$framework = $this->getFramework();

			switch ($framework) {
				case self::FRAMEWORK_FLOW:
					$path = __DIR__ . '/../../../';
					break;

				case self::FRAMEWORK_TYPO3:
					$path = $this->getBasePath() . 'typo3temp/';
					break;

				case self::FRAMEWORK_STANDALONE:
				default:
					$path = sys_get_temp_dir() . '/';
					break;
			}

		}
		return $path;
	}

	/**
	 * Returns the name of the extension from which the iresults method was
	 * called.
	 *
	 * You shouldn't use this in an production environment
	 *
	 * @param    boolean $lowerCaseUnderscored     Set to TRUE if you want the returned value to be in lower_case_underscored
	 * @return    string
	 */
	public function getNameOfCallingPackage($lowerCaseUnderscored = FALSE) {
		return FALSE;
	}


	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

	######## ########     ###    ##    ##  ######  ##          ###    ######## ####  #######  ##    ##
	   ##    ##     ##   ## ##   ###   ## ##    ## ##         ## ##      ##     ##  ##     ## ###   ##
	   ##    ##     ##  ##   ##  ####  ## ##       ##        ##   ##     ##     ##  ##     ## ####  ##
	   ##    ########  ##     ## ## ## ##  ######  ##       ##     ##    ##     ##  ##     ## ## ## ##
	   ##    ##   ##   ######### ##  ####       ## ##       #########    ##     ##  ##     ## ##  ####
	   ##    ##    ##  ##     ## ##   ### ##    ## ##       ##     ##    ##     ##  ##     ## ##   ###
	   ##    ##     ## ##     ## ##    ##  ######  ######## ##     ##    ##    ####  #######  ##    ##

	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	/**
	 * Returns the translated value for the given key.
	 *
	 * @param    string $key              The key to translate
	 * @param    array  $arguments        An optional array of arguments that will be passed to vsprintf()
	 * @param    string $extensionName    Optional extension name. If empty the extension name will be automatically determined
	 * @return    string
	 */
	public function translate($key, array $arguments = array(), $extensionName = '') {
		return $key;
	}

	/**
	 * @see translate()
	 */
	public function __($key, array $arguments = array(), $extensionName = '') {
		return $this->translate($key, $arguments, $extensionName);
	}


	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

	########  ######## ########  ##     ##  ######    ######   #### ##    ##  ######        ####
	##     ## ##       ##     ## ##     ## ##    ##  ##    ##   ##  ###   ## ##    ##      ##  ##
	##     ## ##       ##     ## ##     ## ##        ##         ##  ####  ## ##             ####
	##     ## ######   ########  ##     ## ##   #### ##   ####  ##  ## ## ## ##   ####     ####
	##     ## ##       ##     ## ##     ## ##    ##  ##    ##   ##  ##  #### ##    ##     ##  ## ##
	##     ## ##       ##     ## ##     ## ##    ##  ##    ##   ##  ##   ### ##    ##     ##   ##
	########  ######## ########   #######   ######    ######   #### ##    ##  ######       ####  ##

	##        #######   ######    ######   #### ##    ##  ######
	##       ##     ## ##    ##  ##    ##   ##  ###   ## ##    ##
	##       ##     ## ##        ##         ##  ####  ## ##
	##       ##     ## ##   #### ##   ####  ##  ## ## ## ##   ####
	##       ##     ## ##    ##  ##    ##   ##  ##  #### ##    ##
	##       ##     ## ##    ##  ##    ##   ##  ##   ### ##    ##
	########  #######   ######    ######   #### ##    ##  ######

	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	/**
	 * Logs the given variable.
	 *
	 * @param    mixed   $var         If $var is a scalar it will be written directly, else the output of var_export() is used
	 * @param    integer $code        The error code
	 * @param    string  $logfile     The path to the log file. The default path is /typo3conf/iresults.log
	 * @return    boolean                TRUE on success otherwise FALSE
	 */
	public function log($var, $code = -1, $logfile = -1) {
		// TODO: Implement me
		return FALSE;
	}

//	/**
//	 * Returns if debugging is enabled in the current situation.
//	 *
//	 * @return    boolean
//	 */
//	public function willDebug() {
//		if (self::$willDebug !== -1) return self::$willDebug;
//
//		$willDebugL = TRUE;
//		/**
//		 * LOWEST PRIORITY
//		 * Check if the irdebug parameter was passed or the server settings have
//		 * DEVELOPER_MODE set to TRUE.
//		 */
//		if ((!isset($_GET['irdebug']) || !$_GET['irdebug']) &&
//			(!isset($_SERVER['DEVELOPER_MODE']) || !$_SERVER['DEVELOPER_MODE'])
//		) {
//			$willDebugL = FALSE;
//		}
//
//		/**
//		 * MID PRIORITY
//		 * Check if PRODUCTION_MODE on the server is enabled. This will disable
//		 * the debugging through the irdebug parameter and even the
//		 * DEVELOPER_MODE settting.
//		 */
//		if (isset($_SERVER['PRODUCTION_MODE']) && $_SERVER['PRODUCTION_MODE']) {
//			$willDebugL = FALSE;
//		}
//
//
//		/**
//		 * HIGHEST PRIORITY
//		 * Check if the DEVELOPER_MODE_IP_MASK is set inside the .htaccess file
//		 * and if this matches the current users IP address. If this condition
//		 * is met, debugging is always allowed.
//		 */
//		if (isset($_SERVER['DEVELOPER_MODE_IP_MASK']) &&
//			$_SERVER['REMOTE_ADDR'] === $_SERVER['DEVELOPER_MODE_IP_MASK']
//		) {
//			$willDebugL = TRUE;
//		}
//
//		if (isset($_GET['irdebug']) && !$_GET['irdebug']) {
//			$willDebugL = FALSE;
//		}
//
//		/**
//		 * Get the renderer from the configuration.
//		 */
//		$renderer = intval($this->getConfiguration('debugRenderer'));
//		if ($renderer) {
//			$this->setDebugRenderer($renderer);
//		}
//
//		self::$willDebug = $willDebugL;
//		return self::$willDebug;
//	}


//	/**
//	 * Returns if the path information will be displayed
//	 *
//	 * @return boolean
//	 */
//	public function getDisplayDebugPath() {
//		static $printPathInformation = -1;
//		if ($printPathInformation === -1) {
//			$printPathInformation = (bool)$this->getConfiguration('displayDebugPath');
//		}
//		return $printPathInformation;
//	}

//	/**
//	 * Returns if the current request is a full request (i.e. not an AJAX
//	 * request)
//	 *
//	 * @return    boolean
//	 */
//	public function isFullRequest() {
//		return TRUE;
//	}

//	/**
//	 * Returns the configuration as an array.
//	 *
//	 * If a key is given, the configuration is searched for an entry with the
//	 * given key. If a matching entry exists it will be returned, otherwise
//	 * FALSE. If no key is given, the whole configuration array will be
//	 * returned.
//	 *
//	 * @param    string $key    The key for a configuration entry
//	 * @return    array|mixed    The whole configuration array, or the key's entry or FALSE for an unfound key
//	 */
//	public function getConfiguration($key = NULL) {
//		if (!$key) {
//			return self::$configuration;
//		}
//		if (isset(self::$configuration[$key])) {
//			return self::$configuration[$key];
//		}
//		return FALSE;
//	}
//
//	/**
//	 * Overwrite the configuration at the given key with the new value.
//	 *
//	 * @param    string $key   The key of the configuration to change
//	 * @param    mixed  $value The new configuration value
//	 * @return    void
//	 */
//	public function setConfiguration($key, $value) {
//		self::$configuration[$key] = $value;
//	}
//
//	/**
//	 * Returns the path to the given package's directory
//	 *
//	 * @param string $package Package name
//	 * @return string
//	 */
//	public function getPackagePath($package) {
//		return '';
//	}
}