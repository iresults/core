<?php
namespace Iresults\Core;

/*
 * The MIT License (MIT)
 * Copyright (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
use Iresults\Core\Exception\FatalErrorException;
use Iresults\Core\Exception\UndefinedMethod;
use Iresults\Core\System\Backtrace;


/**
 * Base class of the iresults framework.
 *
 * The Iresults class, or one of it's aliases provide several features from
 * debugging to resolution of class files and paths. It is the base of the
 * framework.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Core
 * @version       3.1.0
 */
abstract class Iresults implements IresultsBaseConstants {
	/**
	 * Key for the $GLOBALS to define an implementation class for the shared
	 * instance
	 */
	const REGISTERED_IMPLEMENTATION_CLASS = 'IRESULTS_REGISTERED_IMPLEMENTATION_CLASS';

	/**
	 * The singleton instance
	 *
	 * @var \Iresults\Core\Iresults
	 */
	static protected $sharedInstance = NULL;

	/**
	 * Name of the shared instance's class
	 *
	 * @var string
	 */
	static protected $implementationClassName = '\\Iresults\\Core\\Base';

	/**
	 * Outputs the debug backtrace.
	 *
	 * @param    integer $level The depth of the backtrace
	 * @return    void
	 *
	 * @deprecated since 3.1.0 use \Iresults\Core\System\Backtrace instead
	 */
	static public function backtrace($level = NULL) {
		if ($level === NULL) {
			$level = -1;
		}
		$backtrace = new Backtrace(0, $level);
		echo $backtrace->render();
	}

	/**
	 * Forwards static calls to the shared instance
	 *
	 * @param string $name
	 * @param array  $arguments
	 * @return mixed
	 * @throws Exception\UndefinedMethod
	 */
	static public function __callStatic($name, $arguments) {
		$sharedInstance = static::getSharedInstance();
		if (method_exists($sharedInstance, $name)) {
			return call_user_func_array(array($sharedInstance, $name), $arguments);
		}
		throw new UndefinedMethod('Method ' . $name . ' not found', 1380729299);
	}


	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

	 ######  #### ##    ##  ######   ##       ######## ########  #######  ##    ##
	##    ##  ##  ###   ## ##    ##  ##       ##          ##    ##     ## ###   ##
	##        ##  ####  ## ##        ##       ##          ##    ##     ## ####  ##
	 ######   ##  ## ## ## ##   #### ##       ######      ##    ##     ## ## ## ##
	      ##  ##  ##  #### ##    ##  ##       ##          ##    ##     ## ##  ####
	##    ##  ##  ##   ### ##    ##  ##       ##          ##    ##     ## ##   ###
	 ######  #### ##    ##  ######   ######## ########    ##     #######  ##    ##

	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	/**
	 * Returns the singleton instance
	 *
	 * @return \Iresults\Core\IresultsBaseInterface
	 */
	static public function getSharedInstance() {
		/*
		 * Check if an instance exists
		 */
		if (!self::$sharedInstance) {
			$implementationClassName = static::_getRegisteredImplementationClassName();
			self::$sharedInstance = new $implementationClassName();
		}
		return self::$sharedInstance;
	}

	/**
	 * Register a class to be used as implementation for the shared instance
	 *
	 * @param string $instanceClassName
	 */
	static public function _registerImplementationClassName($instanceClassName) {
		self::$implementationClassName = $instanceClassName;
	}

	/**
	 * Clears the shared instance
	 *
	 * @internal
	 */
	static public function _destroySharedInstance() {
		self::$sharedInstance = NULL;
	}

	/**
	 * Returns the registered class to be used as implementation for the shared
	 * instance
	 *
	 * The global variable will be checked for an entry with the key
	 * 'IRESULTS_REGISTERED_IMPLEMENTATION_CLASS' (see
	 * Iresults::REGISTERED_IMPLEMENTATION_CLASS).
	 * This allows the definition of an implementation without loading the full
	 * iresults/core stack.
	 * If the global isn't set, self::$implementationClassName will be used,
	 * which defaults to \\Iresults\\Core\\Base
	 *
	 * @throws FatalErrorException if no implementation was found
	 * @return string
	 */
	static protected function _getRegisteredImplementationClassName() {
		if (isset($GLOBALS[self::REGISTERED_IMPLEMENTATION_CLASS]) && $GLOBALS[self::REGISTERED_IMPLEMENTATION_CLASS]) {
			return $GLOBALS[self::REGISTERED_IMPLEMENTATION_CLASS];
		} else if (self::$implementationClassName) {
			return self::$implementationClassName;
		}
		throw new FatalErrorException('No implementation class found', 1380875841);
	}


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
	 * Returns the absolute path to the given resource
	 *
	 * @param  \Iresults\FS\FilesystemInterface|string $resource    Either a filesystem instance or the path of a resource
	 * @return string                                        The absolute path of the resource
	 */
	static public function getPathOfResource($resource) {
		return static::getSharedInstance()->getPathOfResource($resource);
	}

	/**
	 * Returns the URL of the resource.
	 *
	 * @param    \Iresults\FS\FilesystemInterface|string $resource    Either a filesystem instance or the path of a resource
	 * @return    string                                            The URL of the resource
	 */
	static public function getUrlOfResource($resource) {
		return static::getSharedInstance()->getUrlOfResource($resource);
	}


	/**
	 * Returns the path to the base directory of the installation.
	 *
	 * @return    string
	 */
	static public function getBasePath() {
		return static::getSharedInstance()->getBasePath();
	}

	/**
	 * @see getBasePath()
	 */
	static public function getBaseDir() {
		return static::getBasePath();
	}

	/**
	 * Returns the base URL of the index.php file.
	 *
	 * @return    string
	 */
	static public function getBaseURL() {
		return static::getSharedInstance()->getBaseURL();
	}

	/**
	 * Returns the path to the temporary directory.
	 *
	 * @return    string
	 */
	static public function getTempPath() {
		return static::getSharedInstance()->getTempPath();
	}

	/**
	 * @see getTempPath()
	 */
	static public function getTempDir() {
		return static::getTempPath();
	}

	/**
	 * Returns the path to the given package's directory
	 *
	 * @param string $package Package name
	 * @return string
	 */
	static public function getPackagePath($package) {
		return static::getSharedInstance()->getPackagePath($package);
	}

	/**
	 * Returns the URL to the given package's directory
	 *
	 * @param string $package Package name
	 * @return string
	 */
	static public function getPackageUrl($package) {
		return static::getSharedInstance()->getPackageUrl($package);
	}

	/**
	 * Returns the versioned file path for the given file path, or the original
	 * file path, if it doesn't exist.
	 *
	 * @param    string $filePath    The file path to create versions
	 * @return    string
	 */
	static public function createVersionedFilePathForPath($filePath) {
		return static::getSharedInstance()->createVersionedFilePathForPath($filePath);
	}

	/**
	 * Returns the name of the extension from which the iresults method was
	 * called
	 *
	 * You shouldn't use this in an production environment
	 *
	 * @param    boolean $lowerCaseUnderscored     Set to TRUE if you want the returned value to be in lower_case_underscored
	 * @return    string
	 */
	static public function getNameOfCallingPackage($lowerCaseUnderscored = FALSE) {
		return static::getSharedInstance()->getNameOfCallingPackage($lowerCaseUnderscored);
	}

	/**
	 * @see getNameOfCallingPackage()
	 * @deprecated since 3.1 use getNameOfCallingPackage()
	 */
	static public function getNameOfCallingExtension($lowerCaseUnderscored = FALSE) {
		return static::getNameOfCallingPackage($lowerCaseUnderscored);
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
	static public function translate($key, array $arguments = array(), $extensionName = '') {
		return static::getSharedInstance()->translate($key, $arguments, $extensionName);
	}

	/**
	 * @see translate()
	 */
	static public function __($key, array $arguments = array(), $extensionName = '') {
		return static::translate($key, $arguments, $extensionName);
	}

	/**
	 * Returns the current language and country code
	 *
	 * If the language can not be determined "en_US" (english) will be used.
	 *
	 * @return string
	 */
	static public function getLocale() {
		return static::getSharedInstance()->getLocale();
	}

	/**
	 * Returns the ISO2 code of the current language.
	 *
	 * If the language couldn't be determined EN (english) will be used.
	 *
	 * @return string
	 * @deprecated since 3.1.0
	 */
	static public function getLanguage() {
		return strtoupper(substr(static::getLocale(), 0, 2));
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
	static public function log($var, $code = -1, $logfile = -1) {
		return static::getSharedInstance()->log($var, $code, $logfile);
	}

	/**
	 * Dumps a given variable (or the given variables) wrapped into a 'pre' tag.
	 *
	 * @param    mixed $var1
	 * @return    string The printed content
	 */
	static public function pd($var1 = '__iresults_pd_noValue') {
		$arguments = func_get_args();
		return call_user_func_array(array(static::getSharedInstance(), 'pd'), $arguments);
	}

	/**
	 * Outputs the given string, taking the current environment into account.
	 *
	 * @param    string  $message        The message to output
	 * @param    string  $color          An optional ASCII color to apply
	 * @param    boolean $insertBreak    Insert a line break
	 * @return    void
	 */
	static public function say($message, $color = NULL, $insertBreak = TRUE) {
		static::getSharedInstance()->say($message, $color, $insertBreak);
	}

	/**
	 * Sets the debug renderer used by pd().
	 *
	 * @param    integer $debugRenderer The debug renderer as one of the RENDERER constants
	 * @return    integer    Returns the former configuration
	 */
	static public function setDebugRenderer($debugRenderer) {
		return static::getSharedInstance()->setDebugRenderer($debugRenderer);
	}

	/**
	 * Returns if debugging is enabled in the current situation.
	 *
	 * @return    boolean
	 */
	static public function willDebug() {
		return static::getSharedInstance()->willDebug();
	}

	/**
	 * Sets the static $willDebug to TRUE
	 *
	 * If an argument is passed the value will be set to it's value
	 *
	 * @return    boolean    Returns the former setting
	 */
	static public function forceDebug() {
		$flag = TRUE;
		if (func_num_args() > 0) {
			$flag = func_get_arg(0);
		}
		return static::getSharedInstance()->forceDebug($flag);
	}


	/**
	 * Returns if the path information will be displayed
	 *
	 * @return boolean
	 */
	static public function getDisplayDebugPath() {
		return static::getSharedInstance()->getDisplayDebugPath();
	}

	/**
	 * Returns the environment.
	 *
	 * @return    integer|Iresults::ENVIRONMENT    This run's environment
	 */
	static public function getEnvironment() {
		return static::getSharedInstance()->getEnvironment();
	}

	/**
	 * Returns the protocol used for the current request.
	 *
	 * @return    string    Returns 'http', 'https' or whatever protocol was used for the current request
	 */
	static public function getProtocol() {
		return static::getSharedInstance()->getProtocol();
	}

	/**
	 * Returns the output format as one of the OUTPUT_FORMAT constants.
	 *
	 * @return    string|Iresults::OUTPUT_FORMAT
	 */
	static public function getOutputFormat() {
		return static::getSharedInstance()->getOutputFormat();
	}

	/**
	 * Returns the main framework the Iresults framework is used with.
	 *
	 * @return    Iresults::FRAMEWORK|string    The main framework
	 */
	static public function getFramework() {
		return static::getSharedInstance()->getFramework();
	}

	/**
	 * Returns if the current request is a full request (i.e. not an AJAX
	 * request)
	 *
	 * @return    boolean
	 */
	static public function isFullRequest() {
		return static::getSharedInstance()->isFullRequest();
	}

	/**
	 * @see isFullRequest()
	 */
	static public function getIsFullRequest() {
		return static::isFullRequest();
	}

	/**
	 * Returns the current trace level
	 *
	 * The starting depth to determine the file and line number of the original function call in pd()
	 *
	 * @return integer
	 * @internal
	 */
	static public function getTraceLevel() {
		return static::getSharedInstance()->getTraceLevel();
	}

	/**
	 * Sets the current trace level
	 *
	 * The starting depth to determine the file and line number of the original function call in pd()
	 *
	 * @param int $newTraceLevel
	 * @return int Returns the previous value
	 * @internal
	 */
	static public function setTraceLevel($newTraceLevel) {
		return static::getSharedInstance()->setTraceLevel($newTraceLevel);
	}

	/**
	 * Returns a description of the given value.
	 *
	 * @param    mixed $value The value to describe
	 * @return    string    The description text
	 */
	static public function descriptionOfValue($value) {
		return static::getSharedInstance()->descriptionOfValue($value);
	}

	/**
	 * Returns the configuration as an array.
	 *
	 * If a key is given, the configuration is searched for an entry with the
	 * given key. If a matching entry exists it will be returned, otherwise
	 * FALSE. If no key is given, the whole configuration array will be
	 * returned.
	 *
	 * @param    string $key    The key for a configuration entry
	 * @return    array|mixed    The whole configuration array, or the key's entry or FALSE for an unfound key
	 */
	static public function getConfiguration($key = NULL) {
		return static::getSharedInstance()->getConfiguration($key);
	}

	/**
	 * Overwrite the configuration at the given key with the new value.
	 *
	 * @param    string $key   The key of the configuration to change
	 * @param    mixed  $value The new configuration value
	 * @return    void
	 */
	static public function setConfiguration($key, $value) {
		static::getSharedInstance()->setConfiguration($key, $value);
	}


	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

	######## ##     ##  ######  ######## ########  ######## ####  #######  ##    ##       ###    ##    ## ########
	##        ##   ##  ##    ## ##       ##     ##    ##     ##  ##     ## ###   ##      ## ##   ###   ## ##     ##
	##         ## ##   ##       ##       ##     ##    ##     ##  ##     ## ####  ##     ##   ##  ####  ## ##     ##
	######      ###    ##       ######   ########     ##     ##  ##     ## ## ## ##    ##     ## ## ## ## ##     ##
	##         ## ##   ##       ##       ##           ##     ##  ##     ## ##  ####    ######### ##  #### ##     ##
	##        ##   ##  ##    ## ##       ##           ##     ##  ##     ## ##   ###    ##     ## ##   ### ##     ##
	######## ##     ##  ######  ######## ##           ##    ####  #######  ##    ##    ##     ## ##    ## ########

	######## ########  ########   #######  ########     ##     ##    ###    ##    ## ########  ##       #### ##    ##  ######
	##       ##     ## ##     ## ##     ## ##     ##    ##     ##   ## ##   ###   ## ##     ## ##        ##  ###   ## ##    ##
	##       ##     ## ##     ## ##     ## ##     ##    ##     ##  ##   ##  ####  ## ##     ## ##        ##  ####  ## ##
	######   ########  ########  ##     ## ########     ######### ##     ## ## ## ## ##     ## ##        ##  ## ## ## ##   ####
	##       ##   ##   ##   ##   ##     ## ##   ##      ##     ## ######### ##  #### ##     ## ##        ##  ##  #### ##    ##
	##       ##    ##  ##    ##  ##     ## ##    ##     ##     ## ##     ## ##   ### ##     ## ##        ##  ##   ### ##    ##
	######## ##     ## ##     ##  #######  ##     ##    ##     ## ##     ## ##    ## ########  ######## #### ##    ##  ######

	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	/**
	 * The iresults exception handler.
	 *
	 * This method will be used for exception handling in CLI environment.
	 *
	 * @param    \Exception $exception    The exception to handle
	 * @param    boolean    $graceful     Set to TRUE if the handler should not stop the PHP script
	 * @return        void
	 */
	static public function handleException($exception, $graceful = FALSE) {
		static::getSharedInstance()->handleException($exception, $graceful);
	}
}
