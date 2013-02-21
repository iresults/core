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


define('IR_MODERN_PHP', TRUE);


/**
 * Base class of the iresults framework.
 *
 * The Iresults class, or one of it's aliases provide several features from
 * debugging to resolution of class files and paths. It is the base of the
 * framework.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 * @version 3.0.0.0
 */
class Iresults {
	/**
	 * The different debug renderers that can be used by pd().
	 */
	const RENDERER = 'RENDERER';

	/**
	 * Do not render inside the pd method.
	 */
	const RENDERER_NONE = -1;

	/**
	 * Render the variable information inside pd using the class Zend_Debug.
	 */
	const RENDERER_ZEND_DEBUG = 1;

	/**
	 * Render the variable information inside pd using var_dump().
	 */
	const RENDERER_VAR_DUMP = 2;

	/**
	 * Render the variable information inside pd using var_export().
	 */
	const RENDERER_VAR_EXPORT = 3;

	/**
	 * Render the variable information inside pd using the class Iresults_Debug.
	 */
	const RENDERER_IRESULTS_DEBUG = 4;


	/**
	 * The different environment constants for web and shell or CLI.
	 */
	const ENVIRONMENT = 'ENVIRONMENT';

	/**
	 * The environment constant for a web server request.
	 */
	const ENVIRONMENT_WEB = 1;

	/**
	 * The environment for a shell/cli/terminal request.
	 */
	const ENVIRONMENT_SHELL = 2;

	/**
	 * The environment for a shell/cli/terminal request.
	 */
	const ENVIRONMENT_CLI = 2;


	/**
	 * The different output formats.
	 */
	const OUTPUT_FORMAT = 'OUTPUT_FORMAT';

	/**
	 * Some kind of XML data
	 */
	const OUTPUT_FORMAT_XML = 'xml';

	/**
	 * JSON encoded data
	 */
	const OUTPUT_FORMAT_JSON = 'json';

	/**
	 * Plain text data
	 */
	const OUTPUT_FORMAT_PLAIN = 'plain';

	/**
	 * Binary data
	 */
	const OUTPUT_FORMAT_BINARY = 'bin';


	/**
	 * The framework iresults is used with.
	 */
	const FRAMEWORK = 'FRAMEWORK';

	/**
	 * Iresults is used standalone.
	 */
	const FRAMEWORK_STANDALONE = 'standalone';

	/**
	 * Iresults is used in conjunction with TYPO3.
	 */
	const FRAMEWORK_TYPO3 = 'typo3';

	/**
	 * Iresults is used in conjunction with FLOW.
	 */
	const FRAMEWORK_FLOW = 'flow';

	/**
	 * Iresults is used in conjunction with FLOW3.
	 * @deprecated Use FRAMEWORK_FLOW instead
	 */
	const FRAMEWORK_FLOW3 = 'flow';

	/**
	 * Iresults is used in conjunction with Symfony.
	 */
	const FRAMEWORK_SYMFONY = 'symfony';


	/**
	 * @var integer The pd renderer to use as one of the RENDERER constants.
	 */
	static protected $_renderer = 4;

	/**
	 * @var integer Indicates and saves if debugging is enabled.
	 */
	static protected $willDebug = -1;

	/**
	 * @var integer The starting depth to determine the file and line number of the original function call in pd().
	 */
	static protected $traceLevel = -1;

	/**
	 * The environment from which the class is called, as one of the environment constants.
	 *
	 * @var ENVIRONMENT|integer
	 */
	static protected $environment = 1;

	/**
	 * The main framework the Iresults framework is used with.
	 *
	 * @var FRAMEWORK|string
	 */
	static protected $framework = '';

	/**
	 * @var string The cache for the base path.
	 */
	static protected $basePath = '';

	/**
	 * @var string The cache for the base url.
	 */
	static protected $baseURL = '';

	/**
	 * @var array The configuration from ext_conf_template.txt.
	 */
	static protected $configuration = NULL;

	/**
	 * @var \Iresults\Core\Iresults
	 */
	static private $instance = NULL;


	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

	#### ##    ## #### ######## ####    ###    ##       #### ########    ###    ######## ####  #######  ##    ##
	 ##  ###   ##  ##     ##     ##    ## ##   ##        ##       ##    ## ##      ##     ##  ##     ## ###   ##
	 ##  ####  ##  ##     ##     ##   ##   ##  ##        ##      ##    ##   ##     ##     ##  ##     ## ####  ##
	 ##  ## ## ##  ##     ##     ##  ##     ## ##        ##     ##    ##     ##    ##     ##  ##     ## ## ## ##
	 ##  ##  ####  ##     ##     ##  ######### ##        ##    ##     #########    ##     ##  ##     ## ##  ####
	 ##  ##   ###  ##     ##     ##  ##     ## ##        ##   ##      ##     ##    ##     ##  ##     ## ##   ###
	#### ##    ## ####    ##    #### ##     ## ######## #### ######## ##     ##    ##    ####  #######  ##    ##

	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	/**
	 * Initialize the iresults class.
	 *
	 * @return	Iresults
	 */
	public function __construct() {
		if (self::$instance) {
			return NULL;
		}

		/*
		 * Check the debug renderer.
		 */
		self::willDebug();

		/*
		 * Check the environment.
		 */
		if (isset($_SERVER['TERM']) && $_SERVER['TERM']) {
			self::$environment = self::ENVIRONMENT_SHELL;
			ini_set('display_errors', 1);
			#error_reporting(E_ALL);

			set_exception_handler(array(__CLASS__, 'handleException'));
		}

		/*
		 * Check the framework.
		 */
		if (defined('TYPO3_MODE')) {
			self::$framework = self::FRAMEWORK_TYPO3;
		} else {
		foreach (get_declared_classes() as $name) {
			if (strpos($name, 'TYPO3\\FLOW3\\') === 0) {
				self::$framework = self::FRAMEWORK_FLOW3;
				break;
			} else if (strpos($name, 'TYPO3\\Flow\\') === 0) {
				self::$framework = self::FRAMEWORK_FLOW;
				break;
			} else if (strpos($name, 'Symfony\\') === 0) {
				self::$framework = self::FRAMEWORK_SYMFONY;
				break;
			}
		}
		if (!self::$framework) {
			self::$framework = self::FRAMEWORK_STANDALONE;
		}
		}

		self::$instance = $this;
		return $this;
	}


	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

	#### ##    ##  ######  ########    ###    ##    ##  ######  ########     ######  ########  ########    ###    ######## ####  #######  ##    ##
	 ##  ###   ## ##    ##    ##      ## ##   ###   ## ##    ## ##          ##    ## ##     ## ##         ## ##      ##     ##  ##     ## ###   ##
	 ##  ####  ## ##          ##     ##   ##  ####  ## ##       ##          ##       ##     ## ##        ##   ##     ##     ##  ##     ## ####  ##
	 ##  ## ## ##  ######     ##    ##     ## ## ## ## ##       ######      ##       ########  ######   ##     ##    ##     ##  ##     ## ## ## ##
	 ##  ##  ####       ##    ##    ######### ##  #### ##       ##          ##       ##   ##   ##       #########    ##     ##  ##     ## ##  ####
	 ##  ##   ### ##    ##    ##    ##     ## ##   ### ##    ## ##          ##    ## ##    ##  ##       ##     ##    ##     ##  ##     ## ##   ###
	#### ##    ##  ######     ##    ##     ## ##    ##  ######  ########     ######  ##     ## ######## ##     ##    ##    ####  #######  ##    ##

	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
	# MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM




	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	// /**
	//  * Returns an instance of the given class, with the given parameters as
	//  * constructor parameters.
	//  *
	//  * @param	string 	$className
	//  * @param	mixed 	$parameters A parameter to pass to the constructor
	//  * @return	Iresults_Core
	//  */
	// static public function makeInstance($className, $parameters = NULL) {
	// 	if (!class_exists($className)) {
	// 		throw new \InvalidArgumentException('Class ' . $className . ' couldn\'t be loaded.', 1351503157);
	// 	}

	// 	$tempObject = NULL;
	// 	if (func_num_args() > 1) {
	// 		$tempObject = new $className($parameters);
	// 	} else {
	// 		$tempObject = new $className();
	// 	}

	// 	return $tempObject;
	// }

	// /**
	//  * Loads a classfile if the class doesn't already exist.
	//  *
	//  * @param	string 	$className 	The class name to load
	//  * @return	boolean				Returns if the class could be loaded
	//  */
	// static protected function _loadClassFile($className) {
	// 	static $baseDirIsInIncludePath = FALSE;
	// 	$originalClassName = $className;
	// 	$onlyAutoloader = FALSE;
	// 	$loadPath = '';

	// 	/*
	// 	 * Check if it is not an iresults class name
	// 	 */
	// 	if (substr($className, 0, 3) === 'Tx_' && substr($className, 3, 9) !== 'Iresults_') {
	// 		$relPath = $className;
	// 		if (substr($relPath, 0, 3) === 'Tx_') {
	// 			$relPath = substr($relPath, 3);
	// 		}
	// 		$relPath = preg_replace('!_!', '_Classes_', $relPath, 1);
	// 		$relPath = '/' . str_replace('_', '/', $relPath) . '.php';

	// 		if (!defined('PATH_typo3conf')) {
	// 			define('PATH_typo3conf', '');
	// 		}
	// 		$loadPath = PATH_typo3conf . 'ext' . $relPath;
	// 	} else
	// 	/*
	// 	 * Load an iresults class file
	// 	 */
	// 	if (substr($className, 0, 9) === 'Iresults_' || substr($className, 3, 9) === 'Iresults_') {
	// 		// Remove "Tx"
	// 		$relPath = $className;
	// 		if (substr($relPath, 0, 3) === 'Tx_') {
	// 			$relPath = substr($relPath, 3);
	// 		}

	// 		// Replace "Iresults" with "Classes" once
	// 		$relPath = '/' . str_replace('_', '/', $relPath) . '.php';
	// 		if (substr($relPath, 0, 10) === '/Iresults/') {
	// 			$relPath = '/Classes/' . substr($relPath, 10);
	// 		}
	// 		$loadPath = __DIR__ . $relPath;
	// 	} else

	// 	 * Load a class in the include path

	// 	{
	// 		// It is assumed that the Zend library is in the PHP include path.
	// 		if ($baseDirIsInIncludePath === FALSE) {
	// 			set_include_path(get_include_path() . PATH_SEPARATOR . self::getBaseDir());
	// 			$baseDirIsInIncludePath = TRUE;
	// 		}
	// 		$loadPath = str_replace('_', '/', $className) . '.php';
	// 	}

	// 	// Load the file
	// 	try {
	// 		ob_start();
	// 		include_once($loadPath);
	// 		$buffer = ob_get_clean();
	// 	} catch(Exception $e) {
	// 		$buffer = ob_get_clean();
	// 		/*
	// 		 * If Iresults is the only autoloader, tell that something
	// 		 * went wrong.
	// 		 */
	// 		if ($onlyAutoloader) {
	// 			if (self::$environment == self::ENVIRONMENT_SHELL) {
	// 				self::handleException($e);
	// 			} else {
	// 				throw $e;
	// 			}
	// 		}
	// 		return FALSE;
	// 	}
	// 	return (class_exists($originalClassName, FALSE) || class_exists('Tx_' . $originalClassName, FALSE));
	// }

	// /**
	//  * @see _loadClassFile()
	//  */
	// static public function loadClassFile($className) {
	// 	if (!class_exists($className, FALSE)) {
	// 		if (self::_loadClassFile($className)) {
	// 			return TRUE;
	// 		} else if (self::$framework === self::FRAMEWORK_FLOW) {
	// 			return self::_createClass($className);
	// 		} else {
	// 			return FALSE;
	// 		}
	// 	}
	// 	return TRUE;
	// }

	// /**
	//  * Tries to detect the underscored version of the given class name and
	//  * create the namespaced class at runtime.
	//  *
	//  * @param string $className
	//  * @return boolean			Returns if the class could be created
	//  */
	// static protected function _createClass($className) {
	// 	if (!self::_loadClassFile($className)) {
	// 		if (substr($className, 0, 9) === 'Iresults\\') { // Check if it is an Iresults class
	// 			$implementationClass = 'Tx_' . str_replace('\\', '_', $className);
	// 			if (!self::loadClassFile($implementationClass)) {
	// 				return FALSE;
	// 			}
	// 			$slashPosition = strrpos($className, '\\');
	// 			$namespace = substr($className, 0, $slashPosition);
	// 			$class= substr($className, $slashPosition + 1);

	// 			$code = 'namespace ' . $namespace . '; class ' . $class . ' extends \\' . $implementationClass . ' {}';
	// 			eval($code);
	// 		}
	// 	}
	// 	return class_exists($className, FALSE);
	// }


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
	 * @param  Iresults\FilesystemInterface|string	$resource 	Either a filesystem instance or the path of a resource
	 * @return string       									The absolute path of the resource
	 */
	static public function getPathOfResource($resource) {
		$path = '';
		// Get the path if the resource is an object
		if (is_object($resource)) {
			$path = $resource->getPath();
		} else {
			$path = $resource;
		}

		if (defined('TYPO3_MODE')) {
			$tempPath = \t3lib_div::getFileAbsFileName($path);
			if ($tempPath) {
				return $tempPath;
			}
		}
		return realpath($path) ? realpath($path) : $path;
	}

	/**
	 * Returns the URL of the resource.
	 *
	 * @param	Iresults\FilesystemInterface|string	$resource 	Either a filesystem instance or the path of a resource
	 * @return	string											The URL of the resource
	 */
	static public function getUrlOfResource($resource) {
		$url = '';
		$path = $resource;
		$basePath = self::getBasePath();
		$basePathStrLen = strlen($basePath);

		// Get the path if the resource is an object
		if (is_object($resource)) {
			$path = $resource->getPath();
		}

		if (substr($path, 0, $basePathStrLen) === $basePath) {
			$url = substr($path, $basePathStrLen);
			return self::getBaseURL() . $url;
		}
		return $path;
	}


	/**
	 * Returns the path to the base directory of the installation.
	 *
	 * @return	string
	 */
	static public function getBasePath() {
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
	 * @see getBasePath()
	 */
	static public function getBaseDir() {
		return self::getBasePath();
	}

	/**
	 * Returns the base URL of the index.php file.
	 *
	 * @return	string
	 */
	static public function getBaseURL() {
		if (!self::$baseURL) {
			$tempBaseURL = '';
			if (defined('TYPO3_MODE') && $GLOBALS['TSFE']->baseUrl) {
				$tempBaseURL = $GLOBALS['TSFE']->baseUrl;
			} else if (isset($_SERVER['HTTP_ORIGIN'])) {
				$tempBaseURL = $_SERVER['HTTP_ORIGIN'] . $tempBaseURL;
			} else {
				$tempBaseURL = dirname($_SERVER['SCRIPT_NAME']) . '/';
			}
			self::$baseURL = $tempBaseURL;
		}
		return self::$baseURL;
	}

	/**
	 * Returns the path to the temporary directory.
	 *
	 * @return	string
	 */
	static public function getTempPath() {
		static $path = '';
		if (!$path) {
			$framework = self::getFramework();

			switch ($framework) {
				case self::FRAMEWORK_FLOW:
					$path = __DIR__ . '/../../../';
					break;

				case self::FRAMEWORK_TYPO3:
					$path = static::getBaseDir() . 'typo3temp/';
					break;

				case self::FRAMEWORK_STANDALONE:
				default:
					$path = sys_get_temp_dir();
					break;
			}

		}
		return $path;
	}
	/**
	 * @see getTempPath()
	 */
	static public function getTempDir() {
		return self::getTempPath();
	}

	/**
	 * Returns the versioned file path for the given file path, or the original
	 * file path, if it doesn't exist.
	 *
	 * @param	string	$filePath	The file path to create versions
	 * @return	string
	 */
	static public function createVersionedFilePathForPath($filePath) {
		$i = 0;
		$suffix = '';
		$filePathWithoutSuffix = '';

		// Return the original name if it is not occupied
		if (!file_exists($filePath)) {
			return $filePath;
		}

		// Split the suffix from the path
		$lastDotPosition = strrpos($filePath, '.', 1);
		if ($lastDotPosition !== FALSE) {
			$suffix = substr($filePath, $lastDotPosition + 1);
			$filePathWithoutSuffix = substr($filePath, 0, $lastDotPosition);
		} else {
			$filePathWithoutSuffix = $filePath;
		}

		// Loop until the name is not occupied
		do {
			$filePath = $filePathWithoutSuffix . '_' . ++$i . '.' . $suffix;
		} while(file_exists($filePath));
		return $filePath;
	}

	/**
	 * Returns the name of the extension from which the iresults method was
	 * called.
	 *
	 * You shouldn't use this in an production environment.
	 *
	 * @param	boolean	$lowerCaseUnderscored	 Set to TRUE if you want the returned value to be in lower_case_underscored
	 * @return	string
	 */
	static public function getNameOfCallingExtension($lowerCaseUnderscored = FALSE) {
		$extensionName = '';

		// Go back the backtrace and determine the caller.
		$bt = debug_backtrace();
		$level = current($bt);
		do {
			$level = next($bt);
			if (isset($level['class'])) {
				$name = $level['class'];
				$checkName = strtolower($name);
				if (substr($checkName, 0, 3) == 'tx_') $checkName = substr($checkName, 3);
			} else {
				$checkName = '';
			}
		} while(!$checkName ||
				substr($checkName, 0, 8) 	== 'iresults' ||
				substr($checkName, 0, 7) 	== 'extbase' ||
				substr($checkName, 0, 5) 	== 'fluid'
		);

		$nameParts = explode('_', $name);
		if (count($nameParts) <= 1 && strtolower($nameParts[0]) != 'tx') {
			$extensionName = $nameParts[0];
		} else if (count($nameParts) > 1) {
			$extensionName = $nameParts[1];
		}

		if ($lowerCaseUnderscored) {
			$extensionName = Tools\StringTool::camelCaseToLowerCaseUnderscored($extensionName);
		}
		return $extensionName;
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
	 * @param	string 	$key			The key to translate
	 * @param	array 	$arguments		An optional array of arguments that will be passed to vsprintf()
	 * @param	string	$extensionName 	Optional extension name. If empty the extension name will be automatically determined
	 * @return	string
	 */
	static public function translate($key, array $arguments = array(), $extensionName = '') {
		return $key;
	}
	/**
	 * @see translate()
	 */
	static public function __($key, array $arguments = array(), $extensionName = '') {
		return self::translate($key, $arguments, $extensionName);
	}

	/**
	 * Returns the ISO2 code of the current language.
	 *
	 * If the language couldn't be determined EN (english) will be used.
	 *
	 * @return string
	 */
	static public function getLanguage() {
		static $langIso2 = -1;

		if ($langIso2 === -1) {
			if ($_SERVER && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				$acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
				$langIso2 = substr($acceptLanguage, 0, strpos($acceptLanguage, '-'));
			} else {
				$langIso2 = 'EN';
			}
		}
		return $langIso2;
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
	 * @param	mixed	$var		If $var is a scalar it will be written directly, else the output of var_export() is used
	 * @param	integer	$code	    The error code
	 * @param	string	$logfile 	The path to the log file. The default path is /typo3conf/iresults.log
	 * @return	boolean    			TRUE on success otherwise FALSE
	 */
	static public function log($var, $code = -1, $logfile = -1) {
		return Log::log($var, $code, $logfile);
	}

	/**
	 * Dumps a given variable (or the given variables) wrapped into a 'pre' tag.
	 *
	 * @param	mixed	$var1
	 * @return	string The printed content
	 */
	static public function pd($var1 = '__iresults_pd_noValue') {
		static $counter = 0;
		static $scriptDir = '';
		static $didSetDebug = FALSE;
		static $printPathInformation = -1;
		$bt = NULL;
		$output = '';
		$printTags = TRUE;
		$printAnchor = TRUE;
		$outputHandling = 0; // 0 = normal, 1 = shell, 2 >= non XML
		$traceLevel = PHP_INT_MAX;

		if (!self::willDebug()) {
			return;
		}

		// Set debug to TRUE
		if (defined('TYPO3_MODE') && !$didSetDebug && isset($GLOBALS['TSFE'])) {
			$GLOBALS['TSFE']->config['config']['debug'] = 1;
			$didSetDebug = TRUE;
		}

		if ($printPathInformation === -1) {
			$printPathInformation = (bool)static::getConfiguration('displayDebugPath');

			// If no backend user is logged in, doen't show the path info
			if (!isset($GLOBALS['BE_USER'])
				|| !isset($GLOBALS['BE_USER']->user)
				|| !intval($GLOBALS['BE_USER']->user['uid'])) {
				$printPathInformation = FALSE;
			}
		}

		/*
		 * If the environment is a shell or the output type is not XML capture
		 * the output.
		 */
		if (self::$environment === self::ENVIRONMENT_SHELL) {
			$outputHandling = 1;
		} else if (self::getOutputFormat() !== self::OUTPUT_FORMAT_XML) {
			$outputHandling = 2;
		}
		if ($outputHandling) {
			$printAnchor = FALSE;
			$printTags = FALSE;
			ob_start();
		}

		// Output the dumps
		if ($printTags) {
			if ($printAnchor) {
				echo '<a href="#ir_debug_anchor_bottom_' . $counter . '" name="ir_debug_anchor_top_' . $counter . '" style="background:#555;color:#fff;font-size:0.6em;">&gt; bottom</a>';
			}
			echo '<div class="ir_debug_container" style="text-align:left;"><pre class="ir_debug">';
		}
		$args = func_get_args();
		foreach ($args as $var) {
			if ($var !== '__iresults_pd_noValue') {
				if (self::$_renderer == self::RENDERER_ZEND_DEBUG && class_exists('Zend_Debug', FALSE)) {
					Zend_Debug::dump($var);
				} else if (self::$_renderer === self::RENDERER_VAR_DUMP) {
					var_dump($var);
				} else if (self::$_renderer === self::RENDERER_VAR_EXPORT) {
					var_export($var);
					echo PHP_EOL;
				} else if (self::$_renderer === self::RENDERER_IRESULTS_DEBUG) {
					$debug = new \Iresults\Core\Debug($var);
					if ($printTags) {
						echo '<span style="font-size:1.1em;line-height:1.6em;">' . $debug . '</span>';
					} else {
						echo $debug;
					}
					unset($debug);
				} else if (self::$_renderer === self::RENDERER_NONE) {
				} else {
				}
			}
		}


		$i = 0;
		if ($printPathInformation) {
			$bt = NULL;
			$options = FALSE;
			if (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
				$options = DEBUG_BACKTRACE_PROVIDE_OBJECT & DEBUG_BACKTRACE_IGNORE_ARGS;
			}

			if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
				$bt = debug_backtrace($options, 10);
			} else {
				$bt = debug_backtrace($options);
			}

			$function = @$bt[$i]['function'];
			while($function == 'pd' OR $function == 'call_user_func_array' OR
				  $function == 'call_user_func') {
				$i++;
				$function = @$bt[$i]['function'];
			}

			// Get the current trace level
			if ($traceLevel === PHP_INT_MAX) {
				if (isset($_GET['tracelevel'])) {
					$traceLevel = (int) $_GET['tracelevel'];
				} else {
					$traceLevel = self::$traceLevel;
				}
			}
			$i += $traceLevel;

			// Set the static script dir
			if (!$scriptDir) {
				$scriptDir = realpath(self::getBasePath());
				if ($scriptDir === FALSE) {
					$scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
				}
			}

			$file = str_replace($scriptDir, '', @$bt[$i]['file']);
			if ($printTags) {
				echo '<span style="font-size:0.8em">
						<a href="file://' . @$bt[$i]['file'] . '" target="_blank">' . $file . ' @ ' . @$bt[$i]['line'] . '</a>
					</span>';
			} else if ($outputHandling < 2) {
				echo "\033[0;35m" . $file . ' @ ' . @$bt[$i]['line'] . "\033[0m" . PHP_EOL;
			} else {
				echo $file . ' @ ' . @$bt[$i]['line'] . PHP_EOL;
			}
		}

		if ($printTags) {
			echo '</pre></div>';
			if ($printAnchor) {
				echo '<a href="#ir_debug_anchor_top_' . $counter . '" name="ir_debug_anchor_bottom_' . $counter . '" style="background:#555;color:#fff;font-size:0.6em;">&gt; top</a><br />';
				$counter++;
			}
		}

		/*
		 * If the output was captured, read it and write it to the STDOUT.
		 */
		if ($outputHandling) {
			$output = ob_get_contents();
			ob_end_clean();

			if ($outputHandling >= 2) {
				self::say($output);
			} else {
				fwrite(STDOUT, $output);
			}
		}
		return $output;
	}

	/**
	 * Outputs the given string, taking the current environment into account.
	 *
	 * @param	string		$message 		The message to output
	 * @param	string		$color  		An optional ASCII color to apply
	 * @param	boolean		$insertBreak	Insert a line break
	 * @return	void
	 */
	static public function say($message, $color = NULL, $insertBreak = TRUE) {
		/*
		 * If the current environment is a shell, read the captured output.
		 */
		if (self::$environment == self::ENVIRONMENT_SHELL) {
			if ($color) {
				$message = "\033" . $color . $message . "\033[0m";
			}
			if ($insertBreak) {
				$message .= PHP_EOL;
			}
			fwrite(STDOUT, $message);
		} else if (self::getOutputFormat() === self::OUTPUT_FORMAT_JSON) {
			echo '[{ "output": "' . $message . '"},';
		} else {
			echo $message;
		}
	}

	/**
	 * Sets the debug renderer used by pd().
	 *
	 * @param	integer	$debugRenderer The debug renderer as one of the RENDERER constants
	 * @return	integer    Returns the former configuration
	 */
	static public function setDebugRenderer($debugRenderer) {
		$oldRenderer = self::$_renderer;
		self::$_renderer = $debugRenderer;
		return $oldRenderer;
	}

	/**
	 * Outputs the debug backtrace.
	 *
	 * @param	integer	$level The depth of the backtrace
	 * @return	void
	 */
	public function backtrace($level = NULL) {
        $bt = debug_backtrace();
		if ($level === NULL) $level = count($bt);

		echo '<pre>';
		for($i = 1;$i <= $level;$i++) {
			if (!isset($bt[$i]) || !is_array($bt[$i])) continue;
			$cLevel = $bt[$i];
			if (isset($cLevel['class']) && $cLevel['class']) echo @$cLevel['class']."::";
			echo $cLevel['function']." <a href='file://".$cLevel['file']."' target='_blank'>".$cLevel['file']."</a> @ ".$cLevel['line']. PHP_EOL;
		}
		echo '</pre>';
    }

	/**
	 * Returns if debugging is enabled in the current situation.
	 *
	 * @return	boolean
	 */
	static public function willDebug() {
		if (self::$willDebug !== -1) return self::$willDebug;

		$willDebugL = TRUE;
		/**
		 * LOWEST PRIORITY
		 * Check if the irdebug parameter was passed or the server settings have
		 * DEVELOPER_MODE set to TRUE.
		 */
		if (	(!isset($_GET['irdebug']) || !$_GET['irdebug']) &&
			(!isset($_SERVER['DEVELOPER_MODE']) || !$_SERVER['DEVELOPER_MODE'])) {
			$willDebugL = FALSE;
		}

		/**
		 * MID PRIORITY
		 * Check if PRODUCTION_MODE on the server is enabled. This will disable
		 * the debugging through the irdebug parameter and even the
		 * DEVELOPER_MODE settting.
		 */
		if (isset($_SERVER['PRODUCTION_MODE']) && $_SERVER['PRODUCTION_MODE']) {
			$willDebugL = FALSE;
		}


		/**
		 * HIGHEST PRIORITY
		 * Check if the DEVELOPER_MODE_IP_MASK is set inside the .htaccess file
		 * and if this matches the current users IP address. If this condition
		 * is met, debugging is always allowed.
		 */
		if (isset($_SERVER['DEVELOPER_MODE_IP_MASK']) &&
		   $_SERVER['REMOTE_ADDR'] === $_SERVER['DEVELOPER_MODE_IP_MASK']
		   ) {
			$willDebugL = TRUE;
		}

		if (isset($_GET['irdebug']) && !$_GET['irdebug']) {
			$willDebugL = FALSE;
		}

		/**
		 * Get the renderer from the configuration.
		 */
		$renderer = intval(static::getConfiguration('debugRenderer'));
		if ($renderer) {
			self::setDebugRenderer($renderer);
		}

		self::$willDebug = $willDebugL;
		return self::$willDebug;
	}

	/**
	 * Set the static $willDebug to the given flag.
	 *
	 * @param	boolean	$flag
	 * @return	boolean    Returns the former setting
	 */
	static public function forceDebug($flag = TRUE) {
		$oldValue = self::$willDebug;
		self::$willDebug = ($flag) ? TRUE : FALSE;
		return $oldValue;
	}

	/**
	 * Returns the environment.
	 *
	 * @return	ENVIRONMENT|integer    This run's environment
	 */
	static public function getEnvironment() {
		return self::$environment;
	}

	/**
	 * Returns the protocol used for the current request.
	 *
	 * @return	string    Returns 'http', 'https' or whatever protocol was used for the current request
	 */
	static public function getProtocol() {
		$protocol = 'http';

		//if (isset($_SERVER['SERVER_PROTOCOL'])) {
		//	$serverProtocol = $_SERVER['SERVER_PROTOCOL'];
		//}
		//
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$protocol = 'https';
		}
		return $protocol;
	}

	/**
	 * Returns the output format as one of the OUTPUT_FORMAT constants.
	  *
	 * @return	string|OUTPUT_FORMAT
	 */
	static public function getOutputFormat() {
		static $outputFormat = '';
		if ($outputFormat === '') {
			if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT']) {
				$outputFormat = $_SERVER['HTTP_ACCEPT'];
			}

			if (isset($_GET['format']) && htmlspecialchars($_GET['format'])) {
				$outputFormat = htmlspecialchars($_GET['format']);
			}

			$headers = headers_list();
			foreach ($headers as $header) {
				if (substr($header, 0, 13) === 'Content-type:') {
					$outputFormat = trim(substr($header, 13));
					break;
				}
			}

			switch (TRUE) {
				case self::$environment === self::ENVIRONMENT_SHELL:
					$outputFormat = self::OUTPUT_FORMAT_BINARY;
					break;

				case $outputFormat == 'xml':
				case $outputFormat == 'html':
				case strpos($outputFormat, 'text/html'):
				case strpos($outputFormat, 'application/xhtml+xml'):
				case strpos($outputFormat, 'application/xml'):
				case strpos($outputFormat, 'text/xml'):
				case strpos($outputFormat, 'application/atom+xml'):
				case strpos($outputFormat, 'application/rdf+xml'):
				case strpos($outputFormat, 'application/rss+xml'):
				case strpos($outputFormat, 'application/soap+xml'):
				case strpos($outputFormat, 'application/font-woff'):
				case strpos($outputFormat, 'application/xhtml+xml'):
				case strpos($outputFormat, 'application/xml-dtd'):
				case strpos($outputFormat, 'application/xop+xml'):
					$outputFormat = self::OUTPUT_FORMAT_XML;
					break;

				case $outputFormat == 'csv':
				case strpos($outputFormat, 'text/plain'):
				case strpos($outputFormat, 'text/csv'):
				case strpos($outputFormat, 'text/css'):
					$outputFormat = self::OUTPUT_FORMAT_PLAIN;
					break;

				case $outputFormat == 'json':
				case strpos($outputFormat, 'application/json'):
				case strpos($outputFormat, 'application/javascript'):
				case strpos($outputFormat, 'application/ecmascript'):
				case strpos($outputFormat, 'text/javascript'):
					$outputFormat = self::OUTPUT_FORMAT_JSON;
					break;

				case strpos($outputFormat, 'application/pdf'):
				case strpos($outputFormat, 'application/zip'):
				case strpos($outputFormat, 'application/gzip'):
				case strpos($outputFormat, 'application/postscript'):
				case strpos($outputFormat, 'application/octet-stream'):
				case strpos($outputFormat, 'audio/'):
				case strpos($outputFormat, 'image/'):
				case strpos($outputFormat, 'video/'):
				default:
					$outputFormat = self::OUTPUT_FORMAT_BINARY;
					break;
			}
		}
		return $outputFormat;
	}

	/**
	 * Returns the main framework the Iresults framework is used with.
	 *
	 * @return	FRAMEWORK|string    The main framework
	 */
	static public function getFramework() {
		return self::$framework;
	}

	/**
	 * Returns if the current request is a full request.
	 *
	 * Requests with an eID, or the type set to the the AJAX type, are no full
	 * requests.
	 *
	 * @return	boolean
	 */
	static public function isFullRequest() {
		if (defined('TYPO3_MODE')) {
			if (
				(
					isset($_GET['eID'])
					&& htmlspecialchars($_GET['eID'])
				)
				|| (
					isset($GLOBALS['TSFE']) && isset($GLOBALS['TSFE']->pSetup)
					&& isset($GLOBALS['TSFE']->pSetup['config.'])
					&& isset($GLOBALS['TSFE']->pSetup['config.']['disableAllHeaderCode'])
			   )
			   ) {
				return FALSE;
			}
		}
		return TRUE;
	}
	/**
	 * @see isFullRequest()
	 */
	static public function getIsFullRequest() {
		return self::isFullRequest();
	}

	/**
	 * Returns the current trace level.
	 *
	 * The starting depth to determine the file and line number of the original function call in pd().
	 * @return integer
	 */
	static public function getTraceLevel() {
		return self::$tracelevel;
	}

	/**
	 * Sets the current trace level.
	 *
	 * The starting depth to determine the file and line number of the original function call in pd().
	 * @return integer 	Returns the previous value
	 */
	static public function setTraceLevel($newTraceLevel) {
		$lastTraceLevel = self::$traceLevel;
		self::$traceLevel = $newTraceLevel;
		return $lastTraceLevel;
	}

	/**
	 * Returns a description of the given value.
	 *
	 * @param	mixed	$value The value to describe
	 * @return	string    The description text
	 */
	static public function descriptionOfValue($value) {
		static $descCache;
		$string = '';
		$glue = ',' . PHP_EOL . "\t";

		/*
		 * Search for the value in the description hash.
		 */
		$hash = NULL;
		if (!$descCache) {
			$descCache = array();
		}
		if (is_object($value)) {
			$hash = spl_object_hash($value);
			if (isset($descCache[$hash])) {
				return 'Recursion';
			}

			/*
			 * Save the value in the descCache if it is an object.
			 */
			$descCache[$hash] = TRUE;
		}

		if (is_array($value) || $value instanceof Traversable) {
			$elementContainer = array();
			foreach($value as $key => $element) {
				$elementContainer[] = ($key && is_string($key) ? "$key: " : '') . self::descriptionOfValue($element);
			}
			$string = 'Array(' . PHP_EOL . "\t" . implode($elementContainer, $glue) . PHP_EOL . ')';
		} else if (is_object($value) && method_exists($value,'description')) {
			$string = $value->description();
		} else if (is_object($value) && method_exists($value,'__toString')) {
			$string = $value->__toString();
		} else if (is_object($value)) {
			$string = '<'.get_class($value).'>';
		} else {
			$string = $value;
		}

		return $string;
	}

	/**
	 * Returns the configuration as an array.
	 *
	 * If a key is given, the configuration is searched for an entry with the
	 * given key. If a matching entry exists it will be returned, otherwise
	 * FALSE. If no key is given, the whole configuration array will be
	 * returned.
	 *
	 * @param	string	$key	The key for a configuration entry
	 * @return	array|mixed	The whole configuration array, or the key's entry or FALSE for an unfound key
	 */
	static public function getConfiguration($key = NULL) {
		if (!$key) {
			return self::$configuration;
		}
		if (isset(self::$configuration[$key])) {
			return self::$configuration[$key];
		}
		return FALSE;
	}

	/**
	 * Overwrite the configuration at the given key with the new value.
	 *
	 * @param	string	$key   The key of the configuration to change
	 * @param	mixed	$value The new configuration value
	 * @return	void
	 */
	static public function setConfiguration($key, $value) {
		self::$configuration[$key] = $value;
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
	 * Returns the singleton instance.
	 * @return \Iresults\Core\Iresults
	 */
	static public function getInstance() {
		if (!self::$instance) {
			self::$instance = new static();
		}
		return self::$instance;
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
	 * @param	Exception 	$exception 	The exception to handle
	 * @param	boolean		$graceful 	Set to TRUE if the handler should not stop the PHP script
	 * @return		void
	 */
	static public function handleException($exception, $graceful = FALSE) {
		$output = 'Uncaught exception #' . $exception->getCode() . ': ' . $exception->getMessage();
		if (self::$willDebug === TRUE) {
			$output = PHP_EOL . "\033[7;31m" . $output . "\033[0m";
			$output .= PHP_EOL . $exception->getTraceAsString() . PHP_EOL;
		} else {
			$output .= PHP_EOL;
		}

		if (self::$environment === self::ENVIRONMENT_CLI) {
			fwrite(STDOUT, $output);
		} else {
			echo $output;
		}

		if (!$graceful) {
			die();
		}
	}
}
?>
