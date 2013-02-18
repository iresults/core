<?php
namespace Iresults\Core;

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
 * Helper class for the use of the service "PHPExcel".
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Excel
 */
class Profiler {
	/**
	 * @var boolean Indicates if profiling is enabled.
	 */
	static protected $enabled = TRUE;

	/**
	 * @var integer Indicates and saves if the debugger is enabled.
	 */
	static protected $checkedEnabled = -1;

	/**
	 * @var boolean Indicates if the path information should be displayed.
	 */
	static protected $printPathInfo = -1;

	/**
	 * @var integer The time the profiler was started.
	 */
	static protected $startTime = 0.0;

	/**
	 * Throws an exception.
	 *
	 * @return	void
	 */
	public function __construct() {
		throw new \Exception('You must not create an instance of the class Iresults_Profiler', 1361180987);
	}

	/**
	 * Prints profiling information like the current time and the time interval
	 * since this function was called first.
	 *
	 * @param	string	$msg			A message to output in the profiler
	 * @param	boolean	$print		If set to false the profiler output will not
	 * be echoed but returned.
	 *
	 * @return	string    The message
	 */
	static public function profile($msg = '', $print = TRUE) {
		if (self::_willProfile()) {
			if (!self::$startTime) {
				self::$startTime = microtime(TRUE);

				// Handle an TYPO3 CMS environment
				if (defined('TYPO3_MODE')) {
					// Set debug to true
					if (isset($GLOBALS['TSFE'])) {
						$GLOBALS['TSFE']->config['config']['debug'] = 1;
					}

					// Look if TYPO3 has messured the time
					if (isset($TYPO3_MISC) && isset($TYPO3_MISC['microtime_start'])) {
						self::$startTime = $TYPO3_MISC['microtime_start'];
					}
				}
			}
			$uptime = sprintf('%0.6f',(microtime(TRUE) - self::$startTime));

			if ($msg) {
				$msg = " MSG: $msg";
			}
			$msg = sprintf('%0.6f',microtime(TRUE)) . "$msg \t Uptime $uptime";

			if ($print) {
				if (\Iresults\Core\Iresults::getEnvironment() == \Iresults\Core\Iresults::ENVIRONMENT_SHELL) {
					\Iresults\Core\Iresults::say($msg, \Iresults\Core\Command\ColorInterface::GREEN);
				} else {
					echo "<pre class='ir_profile'>$msg</pre>";
				}
				if (self::$printPathInfo) {
					self::printPathInfo();
				}
			}
			return $msg;
		}
	}

	/**
	 * Prints information about the current memory usage of the script.
	 *
	 * @param	boolean	$print	 If set to false the profiler output will not
	 * be echoed but returned.
	 *
	 * @return	string    The message
	 */
	static public function memory($print = TRUE) {
		if (!self::_willProfile()) return "";

		$msg = sprintf("Current memory usage is %0.3f MB", (memory_get_usage() / 1024.0 / 1024.0));
		if ($print) {
			if (\Iresults\Core\Iresults::getEnvironment() == \Iresults\Core\Iresults::ENVIRONMENT_SHELL) {
				Iresults::say($msg, \Iresults\Core\Command\ColorInterface::BOLD_GREEN);
			} else {
				echo "<pre class='ir_profile ir_profile_memory'>$msg</pre>\n";
			}
			if (self::$printPathInfo) {
				self::printPathInfo();
			}
		}
		return $msg;
	}

	/**
	 * Determines and outputs the file and line on which the function was called.
	 *
	 * @return	void
	 */
	static public function printPathInfo() {
		$bt = debug_backtrace();
		$i = 0;
		$function = @$bt[$i]['function'];
		while ($function === 'printPathInfo' OR $function === 'profile' OR substr($function, 0, 14) === 'call_user_func') {
			$i++;
			$function = @$bt[$i]['function'];
		}

		if (isset($_GET['tracelevel'])) {
			$i += $_GET['tracelevel'];
		} else {
			$i -= 1;
		}

		$file = str_replace(dirname($_SERVER['SCRIPT_FILENAME']), '', $bt[$i]['file']);
		if (\Iresults\Core\Iresults::getEnvironment() == \Iresults\Core\Iresults::ENVIRONMENT_SHELL) {
			\Iresults\Core\Iresults::say($file . ' @ ' . $bt[$i]['line'], \Iresults\Core\Command\ColorInterface::MAGENTA);
		} else {
			echo "<span style='font-size:0.8em'>
					<a href='file://" . $bt[$i]['file'] . "#" . $bt[$i]['line'] . "' target='_blank'>" . $file . " @ " . $bt[$i]['line'] . "</a>
				</span>";
		}

	}

	/**
	 * Returns if the profiler is on.
	 *
	 * @return	boolean
	 */
	static protected function _willProfile() {
		if (self::$checkedEnabled === -1) {
			if (isset($_SERVER['DEVELOPER_PROFILER']) && $_SERVER['DEVELOPER_PROFILER'] && self::$enabled) {
				self::$checkedEnabled = TRUE;
			} else if (isset($_GET['irprofile']) && $_GET['irprofile']) {
				self::$checkedEnabled = TRUE;
			} else {
				self::$checkedEnabled = FALSE;
			}
			if (self::$checkedEnabled && isset($_GET['irnohtml']) && $_GET['irnohtml']) {
				self::$checkedEnabled = 2;
			}
			if (isset($_SERVER['PRODUCTION_MODE']) && $_SERVER['PRODUCTION_MODE']) {
				self::$checkedEnabled = FALSE;
			}

			/*
			 * If self::$printPathInfo has never been set, read it from the
			 * configuration defined in the configuration manager.
			 */
			if (self::$printPathInfo === -1) {
				self::$printPathInfo = (bool)Iresults::getConfiguration('displayDebugPath');
			}
		}

		return self::$checkedEnabled;
	}

	/**
	 * Sets the profilers start time.
	 *
	 * @return	string Error message or an empty string if the profiler started
	 * correctly.
	 */
	static public function start() {
		if (!self::$startTime) {
			 self::$startTime = microtime(TRUE);
			 return '';
		} else {
			$uptime = sprintf('%0.6f', (microtime(TRUE) - self::$startTime));
			return "<pre>Profiler already started at $uptime.</pre>";
		}
	}

	/**
	 * Enables the profiler
	 *
	 * @return	void
	 */
	static public function enable() {
		self::$enabled = TRUE;
	}

	/**
	 * Disable the profiler
	 *
	 * @return	void
	 */
	static public function disabled() {
		self::$enabled = FALSE;
	}

	/**
	 * Returns if the profiler is enabled
	 *
	 * @return	boolean
	 */
	static public function isEnabled() {
		return self::$enabled;
	}

	/**
	 * Returns if the path information should be displayed.
	 * @return	boolean
	 */
	static public function getPrintPathInfo() {
		return self::$printPathInfo;
	}

	/**
	 * Set if the path information should be displayed.
	 *
	 * @param	boolean	$newValue The new value to set
	 * @return	boolean	Returns the previous value
	 */
	static public function setPrintPathInfo($newValue) {
		$old = self::$printPathInfo;
		self::$printPathInfo = $newValue;
		return $old;
	}
}