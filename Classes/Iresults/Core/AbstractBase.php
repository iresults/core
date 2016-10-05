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
 *         Created 02.10.13 17:35
 */


namespace Iresults\Core;

/**
 * An abstract base class of the iresults framework
 *
 * One implementation of the shared instance for \Iresults\Core\Iresults which
 * does not implement the methods that are likely to have to be changed on
 * different platforms (frameworks)
 *
 * @package Iresults\Core
 */
abstract class AbstractBase implements IresultsBaseInterface {
	/**
	 * @var integer The pd renderer to use as one of the RENDERER constants
	 */
	static protected $_renderer = 4;

	/**
	 * @var integer Indicates and saves if debugging is enabled
	 */
	static protected $willDebug = -1;

	/**
	 * The starting depth to determine the file and line number of the original function call in pd()
	 *
	 * @var integer
	 */
	static protected $traceLevel = -1;

	/**
	 * The environment from which the class is called, as one of the environment constants
	 *
	 * @var Iresults::ENVIRONMENT|integer
	 */
	static protected $environment = 1;

	/**
	 * The main framework the Iresults framework is used with
	 *
	 * @var Iresults::FRAMEWORK|string
	 */
	static protected $framework = '';

	/**
	 * The cache for the base path
	 *
	 * @var string
	 */
	static protected $basePath = '';

	/**
	 * The cache for the base url
	 *
	 * @var string
	 */
	static protected $baseURL = '';

	/**
	 * The main configuration (if used with TYPO3 CMS it will be read from ext_conf_template.txt)
	 *
	 * @var array
	 */
	static protected $configuration = NULL;


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
	 */
	public function __construct() {
		/*
		 * Check the debug renderer.
		 */
		$this->willDebug();

		/*
		 * Check the environment.
		 */
		if (php_sapi_name() === 'cli') {
			self::$environment = self::ENVIRONMENT_SHELL;
			set_exception_handler(array($this, 'handleException'));
		}

		/*
		 * Check the framework.
		 */
		if (defined('TYPO3_MODE')) {
			self::$framework = self::FRAMEWORK_TYPO3;
		} else {
			foreach (get_declared_classes() as $name) {
				if (strpos($name, 'TYPO3\\Flow\\') === 0) {
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
	public function getPathOfResource($resource) {
		$path = '';
		// Get the path if the resource is an object
		if (is_object($resource)) {
			$path = (string)$resource->getPath();
		} else {
			$path = $resource;
		}
		$realPath = realpath($path);
		return $realPath ? $realPath : $path;
	}

	/**
	 * Returns the URL of the resource.
	 *
	 * @param    \Iresults\FS\FilesystemInterface|string $resource    Either a filesystem instance or the path of a resource
	 * @return    string                                            The URL of the resource
	 */
	public function getUrlOfResource($resource) {
		$url = '';
		$path = $resource;
		$basePath = $this->getBasePath();
		$basePathStrLen = strlen($basePath);

		// Get the path if the resource is an object
		if (is_object($resource)) {
			$path = $resource->getPath();
		}

		if (substr($path, 0, $basePathStrLen) === $basePath) {
			$url = substr($path, $basePathStrLen);
			return $this->getBaseURL() . $url;
		}
		return $path;
	}

	/**
	 * Returns the versioned file path for the given file path, or the original
	 * file path, if it doesn't exist.
	 *
	 * @param    string $filePath    The file path to create versions
	 * @return    string
	 */
	public function createVersionedFilePathForPath($filePath) {
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
		} while (file_exists($filePath));
		return $filePath;
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
	 * Returns the current language and country code
	 *
	 * If the language can not be determined "en_US" (english) will be used.
	 *
	 * @return string
	 */
	public function getLocale() {
		static $locale = -1;

		if ($locale === -1) {
			if ($_SERVER && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				$acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
				$locale = substr($acceptLanguage, 0, strpos($acceptLanguage, ','));
				$locale = str_replace('-', '_', $locale);
			} else {
				$locale = 'en_US';
			}
		}
		return $locale;
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
	 * Dumps a given variable (or the given variables) wrapped into a 'pre' tag.
	 *
	 * @param    mixed $var1
	 * @return    string The printed content
	 */
	public function pd($var1 = '__iresults_pd_noValue') {
		static $counter = 0;
		static $scriptDir = '';
		$backtrace = NULL;
		$output = '';
		$printTags = TRUE;
		$printAnchor = TRUE;
		$outputHandling = 0; // 0 = normal, 1 = shell, 2 >= non XML
		$traceLevel = PHP_INT_MAX;

		if (!$this->willDebug()) {
			return '';
		}

		$printPathInformation = $this->getDisplayDebugPath();


		/*
		 * If the environment is a shell or the output type is not XML capture
		 * the output.
		 */
		if (self::$environment === self::ENVIRONMENT_SHELL) {
			$outputHandling = 1;
		} else if ($this->getOutputFormat() !== self::OUTPUT_FORMAT_XML) {
			$outputHandling = 2;
		}
		if ($outputHandling) {
			$printAnchor = FALSE;
			$printTags = FALSE;
			ob_start();
		} else {
			$this->sendDebugHeaders();
		}


		$args = func_get_args();

		/** @var bool $isFullDebugRenderer */
		$isFullDebugRenderer = (self::$_renderer === self::RENDERER_KINT);
		if ($isFullDebugRenderer) {
			switch (self::$_renderer) {
				case self::RENDERER_KINT:
					call_user_func_array(array('Kint', 'dump'), $args);
					break;
			}
		}

		// If the debug renderer provides all information don't step into below
		if (!$isFullDebugRenderer) {

			// Output the dumps
			if ($printTags) {
				if ($printAnchor) {
					echo '<a href="#ir_debug_anchor_bottom_' . $counter . '" name="ir_debug_anchor_top_' . $counter . '" style="background:#555;color:#fff;font-size:0.6em;">&gt; bottom</a>';
				}
				echo '<div class="ir_debug_container" style="text-align:left;"><pre class="ir_debug">';
			}

			foreach ($args as $var) {
				if ($var !== '__iresults_pd_noValue') {
					switch (self::$_renderer) {
						case self::RENDERER_ZEND_DEBUG:
							\Zend_Debug::dump($var);
							break;

						case self::RENDERER_KINT:
							\Kint::dump($var);
							break;

						case self::RENDERER_VAR_DUMP:
							var_dump($var);
							break;

						case self::RENDERER_VAR_EXPORT:
							var_export($var);
							echo PHP_EOL;
							break;

						case self::RENDERER_IRESULTS_DEBUG:
							$debug = new \Iresults\Core\Debug($var);
							if ($printTags) {
								echo '<span style="font-size:1.1em;line-height:1.6em;">' . $debug . '</span>';
							} else {
								echo $debug;
							}
							unset($debug);
							break;

						case self::RENDERER_NONE:
						default:
					}
				}
			}
		}


		$i = 0;
		if ($printPathInformation) {
			$backtrace = NULL;
			$options = FALSE;
			if (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
				$options = DEBUG_BACKTRACE_PROVIDE_OBJECT & DEBUG_BACKTRACE_IGNORE_ARGS;
			}

			if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
				$backtrace = debug_backtrace($options, 10);
			} else {
				$backtrace = debug_backtrace($options);
			}

			$function = @$backtrace[$i]['function'];
			while ($function == 'pd' OR $function == 'call_user_func_array' OR
				$function == 'call_user_func') {
				$i++;
				$function = @$backtrace[$i]['function'];
			}

			// Get the current trace level
			if ($traceLevel === PHP_INT_MAX) {
				if (isset($_GET['tracelevel'])) {
					$traceLevel = (int)$_GET['tracelevel'];
				} else {
					$traceLevel = self::$traceLevel;
				}
			}
			$i += $traceLevel;

			// Set the static script dir
			if (!$scriptDir) {
				$scriptDir = realpath($this->getBasePath());
				if ($scriptDir === FALSE) {
					$scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
				}
			}

			$file = str_replace($scriptDir, '', @$backtrace[$i]['file']);
			if ($printTags) {
				echo '<span style="font-size:0.7em;font-family:Menlo,monospace"><a href="file://' . @$backtrace[$i]['file'] . '" target="_blank">' . $file . ' @ ' . @$backtrace[$i]['line'] . '</a></span>';
			} else if ($outputHandling < 2) {
				echo "\033[0;35m" . $file . ' @ ' . @$backtrace[$i]['line'] . "\033[0m" . PHP_EOL;
			} else {
				echo $file . ' @ ' . @$backtrace[$i]['line'] . PHP_EOL;
			}
		}

		if ($printTags && !$isFullDebugRenderer) {
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
				$this->say($output);
			} else {
				fwrite(STDOUT, $output);
			}
		}
		return $output;
	}

	/**
	 * Outputs the given string, taking the current environment into account.
	 *
	 * @param    string  $message        The message to output
	 * @param    string  $color          An optional ASCII color to apply
	 * @param    boolean $insertBreak    Insert a line break
	 * @return    void
	 */
	public function say($message, $color = NULL, $insertBreak = TRUE) {
		switch (TRUE) {
			case self::$environment == self::ENVIRONMENT_SHELL:
				if ($color) {
					$message = "\033" . $color . $message . "\033[0m";
				}
				if ($insertBreak) {
					$message .= PHP_EOL;
				}
				fwrite(STDOUT, $message); // Use fwrite and return
				return;
			case $this->getOutputFormat() === self::OUTPUT_FORMAT_JSON:
//				$message = json_encode(array('output' => $message)) . PHP_EOL;
				$message = '[{ "output": "' . $message . '"},';
				break;

			case $this->getOutputFormat() === self::OUTPUT_FORMAT_XML:
				if ($insertBreak) {
					$message .= '<br />' . PHP_EOL;
				}
				break;

			default:
				if ($insertBreak) {
					$message .= PHP_EOL;
				}
		}
		echo $message;
	}

	/**
	 * Sets the debug renderer used by pd()
	 *
	 * @param    integer|Iresults::RENDERER $debugRenderer The debug renderer as one of the RENDERER constants
	 * @return    integer|Iresults::RENDERER    Returns the former configuration
	 */
	public function setDebugRenderer($debugRenderer) {
		$oldRenderer = self::$_renderer;
		self::$_renderer = $debugRenderer;
		return $oldRenderer;
	}

	/**
	 * Returns if debugging is enabled in the current situation.
	 *
	 * @return    boolean
	 */
	public function willDebug() {
		if (self::$willDebug !== -1) return self::$willDebug;

		$willDebugL = TRUE;
		/**
		 * LOWEST PRIORITY
		 * Check if the irdebug parameter was passed or the server settings have
		 * DEVELOPER_MODE set to TRUE.
		 */
		if ((!isset($_GET['irdebug']) || !$_GET['irdebug']) &&
			(!isset($_SERVER['DEVELOPER_MODE']) || !$_SERVER['DEVELOPER_MODE'])
		) {
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
		$renderer = intval($this->getConfiguration('debugRenderer'));
		if ($renderer) {
			$this->setDebugRenderer($renderer);
		}

		self::$willDebug = $willDebugL;
		return self::$willDebug;
	}

	/**
	 * Set the static $willDebug to the given flag.
	 *
	 * @param    boolean $flag
	 * @return    boolean    Returns the former setting
	 */
	public function forceDebug($flag = TRUE) {
		$oldValue = self::$willDebug;
		self::$willDebug = ($flag) ? TRUE : FALSE;
		return $oldValue;
	}

	/**
	 * Send the headers to enable UTF-8 output after debugging
	 *
	 * @param bool $graceful
	 * @throws \UnexpectedValueException if the headers already have been sent
	 * @return boolean
	 */
	public function sendDebugHeaders($graceful = TRUE) {
		$file = '';
		$line = '';
		if (!headers_sent($file, $line)) {
			switch ($this->getOutputFormat()) {
				case self::OUTPUT_FORMAT_XML:
					header('Content-Type: text/html; charset=utf-8');
					break;

				case self::OUTPUT_FORMAT_JSON:
					header('Content-Type: application/json; charset=utf-8');
					break;

				case self::OUTPUT_FORMAT_PLAIN:
					header('Content-Type: text/plain; charset=utf-8');
					break;

				case self::OUTPUT_FORMAT_BINARY:
				default:
					break;
			}
		} else if (!$graceful) {
			throw new \UnexpectedValueException("Headers already sent in $file @ $line", 1405001760);
		}

	}


	/**
	 * Returns if the path information will be displayed
	 *
	 * @return boolean
	 */
	public function getDisplayDebugPath() {
		static $printPathInformation = -1;
		if ($printPathInformation === -1) {
			$printPathInformation = (bool)$this->getConfiguration('displayDebugPath');
		}
		return $printPathInformation;
	}

	/**
	 * Returns the environment.
	 *
	 * @return    integer|Iresults::ENVIRONMENT    This run's environment
	 */
	public function getEnvironment() {
		return self::$environment;
	}

	/**
	 * Returns the protocol used for the current request.
	 *
	 * @return    string    Returns 'http', 'https' or whatever protocol was used for the current request
	 */
	public function getProtocol() {
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
	 * @return    string|Iresults::OUTPUT_FORMAT
	 */
	public function getOutputFormat() {
		static $outputFormat = '';
		if ($outputFormat === '') {
			if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT']) {
				$outputFormat = $_SERVER['HTTP_ACCEPT'];
				$outputFormat = strtok($outputFormat, ',');
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
				case $outputFormat == 'text/html':
				case $outputFormat == 'application/xhtml+xml':
				case $outputFormat == 'application/xml':
				case $outputFormat == 'text/xml':
				case $outputFormat == 'application/atom+xml':
				case $outputFormat == 'application/rdf+xml':
				case $outputFormat == 'application/rss+xml':
				case $outputFormat == 'application/soap+xml':
				case $outputFormat == 'application/font-woff':
				case $outputFormat == 'application/xml-dtd':
				case $outputFormat == 'application/xop+xml':
					$outputFormat = self::OUTPUT_FORMAT_XML;
					break;

				case $outputFormat == 'csv':
				case $outputFormat == 'text/plain':
				case $outputFormat == 'text/csv':
				case $outputFormat == 'text/css':
					$outputFormat = self::OUTPUT_FORMAT_PLAIN;
					break;

				case $outputFormat == 'json':
				case $outputFormat == 'application/json':
				case $outputFormat == 'application/javascript':
				case $outputFormat == 'application/ecmascript':
				case $outputFormat == 'text/javascript':
					$outputFormat = self::OUTPUT_FORMAT_JSON;
					break;

				case $outputFormat == 'application/pdf':
				case $outputFormat == 'application/zip':
				case $outputFormat == 'application/gzip':
				case $outputFormat == 'application/postscript':
				case $outputFormat == 'application/octet-stream':
				case strpos($outputFormat, 'audio/') !== FALSE:
				case strpos($outputFormat, 'image/') !== FALSE:
				case strpos($outputFormat, 'video/') !== FALSE:
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
	 * @return    Iresults::FRAMEWORK|string    The main framework
	 */
	public function getFramework() {
		return self::$framework;
	}

	/**
	 * Returns if the current request is a full request (i.e. not an AJAX
	 * request)
	 *
	 * @return    boolean
	 */
	public function isFullRequest() {
		return TRUE;
	}

	/**
	 * @see isFullRequest()
	 */
	public function getIsFullRequest() {
		return $this->isFullRequest();
	}

	/**
	 * Returns the current trace level.
	 *
	 * The starting depth to determine the file and line number of the original function call in pd().
	 *
	 * @return integer
	 */
	public function getTraceLevel() {
		return self::$traceLevel;
	}

	/**
	 * Sets the current trace level.
	 *
	 * The starting depth to determine the file and line number of the original function call in pd().
	 *
	 * @param int $newTraceLevel
	 * @return int Returns the previous value
	 */
	public function setTraceLevel($newTraceLevel) {
		$lastTraceLevel = self::$traceLevel;
		self::$traceLevel = $newTraceLevel;
		return $lastTraceLevel;
	}

	/**
	 * Returns a description of the given value.
	 *
	 * @param    mixed $value The value to describe
	 * @return    string    The description text
	 */
	public function descriptionOfValue($value) {
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

		if (is_array($value) || $value instanceof \Traversable) {
			$elementContainer = array();
			foreach ($value as $key => $element) {
				$elementContainer[] = ($key && is_string($key) ? "$key: " : '') . $this->descriptionOfValue($element);
			}
			$string = 'Array(' . PHP_EOL . "\t" . implode($elementContainer, $glue) . PHP_EOL . ')';
		} else if (is_object($value) && method_exists($value, 'description')) {
			$string = $value->description();
		} else if (is_object($value) && method_exists($value, '__toString')) {
			$string = $value->__toString();
		} else if (is_object($value)) {
			$string = '<' . get_class($value) . '>';
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
	 * @param    string $key    The key for a configuration entry
	 * @return    array|mixed    The whole configuration array, or the key's entry or FALSE for an unfound key
	 */
	public function getConfiguration($key = NULL) {
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
	 * @param    string $key   The key of the configuration to change
	 * @param    mixed  $value The new configuration value
	 * @return    void
	 */
	public function setConfiguration($key, $value) {
		self::$configuration[$key] = $value;
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
	 * The iresults exception handler
	 *
	 * This method will be used for exception handling in CLI environment
	 *
	 * @param    \Exception $exception    The exception to handle
	 * @param    boolean    $graceful     Set to TRUE if the handler should not stop the PHP script
	 * @return        void
	 */
	public function handleException($exception, $graceful = FALSE) {
		$isCliEnvironment = self::$environment === self::ENVIRONMENT_CLI;

		if ($exception instanceof \Exception) {
			$output = 'Uncaught exception #' . $exception->getCode() . ': ' . $exception->getMessage();
		} else {
			$output = 'Uncaught error of type ' . (is_object($exception) ? get_class($exception) : gettype($exception));
		}
		if (self::$willDebug === TRUE) {
			if (!$isCliEnvironment) {
				$output = '<pre>' . $output . PHP_EOL;
			} else {
				$output = PHP_EOL . "\033[7;31m" . $output . "\033[0m";
			}

			if ($exception instanceof \Exception) {
				$output .= PHP_EOL . $exception->getTraceAsString() . PHP_EOL;
			}

			if (!$isCliEnvironment) {
				$output .= '</pre>';
			}
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