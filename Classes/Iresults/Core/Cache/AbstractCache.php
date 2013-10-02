<?php
namespace Iresults\Core\Cache;

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


/**
 * Abstract class for the cache classes. Defines a common interface.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 */
abstract class AbstractCache extends \Iresults\Core\Core implements \Iresults\Core\KVCInterface {
	/**
	 * Constant to force the clearing of the cache through a GET parameter.
	 */
	const CLEAR_CACHE = 'iresults_cache_clear_cache';

	/**
	 * Deactivate the scope of the cache. So the scope has no limit.
	 */
	const SCOPE_NONE = 0;

	/**
	 * Limit the scope of the cache to the current language.
	 */
	const SCOPE_LANGUAGE = 1;

	/**
	 * @var \Iresults\Core\Cache\Abstract The instance of the cache.
	 */
	static protected $instance = NULL;

	/**
	 * @var string The suffix of the cache file. This is used to make the cache
	 * language dependent.
	 */
	static protected $_languageSuffix = '';

	/**
	 * @var integer Defines the scope of the cache as one of the SCOPE constants.
	 */
	static protected $scope = 1;

	/**
	 * Constructor
	 */
	public function __construct() {
		if (isset($_GET[self::CLEAR_CACHE]) && $_GET[self::CLEAR_CACHE]) {
			$this->clear();
		}
	}

	/**
	 * Returns the cache adapter that is supported by the server
	 *
	 * @return \Iresults\Core\Cache\AbstractCache
	 */
	static public function getSharedInstance() {
		$temp = NULL;
		$configuration = 'auto';
		if (self::$instance) {
			return self::$instance;
		}

		/*
		 * Set the tracelevel to check where the cache was instantiated.
		 * TODO: Remove the tracelevel stuff
		 */
		$oldTraceLevel = NULL;
		if (isset($_GET['tracelevel'])) {
			$oldTraceLevel = intval($_GET['tracelevel']);
		}
		$_GET['tracelevel'] = 0;

		/*
		 * If the configuration is set to 'auto', check which caches are
		 * available.
		 */
		if ($configuration === 'auto') {
			switch (TRUE) {
				case is_callable('apc_store'):
					$configuration = 'APC';
					break;

				case is_callable('wincache_ucache_get'):
					$configuration = 'WinCache';
					break;

				case class_exists('\\Iresults\\Core\\Cache\\Ir'):
					$configuration = 'IR';
					break;

				default:
					$configuration = 'none';
			}
		}

		/*
		 * Create the cache instance according to the configuration.
		 */
		switch($configuration) {
			case 'APC':
				\Iresults\Core\Iresults::pd('Init cache: Using APC cache.');
				$temp = new \Iresults\Core\Cache\APC();
				break;

			case 'WinCache':
				\Iresults\Core\Iresults::pd('Init cache: Using WinCache.');
				$temp = new \Iresults\Core\Cache\WinCache();
				break;

			case 'IR':
				\Iresults\Core\Iresults::pd('Init cache: Using Ir cache');
				$temp = new \Iresults\Core\Cache\Ir();
				break;

			case 'none':
			default:
				\Iresults\Core\Iresults::pd('Init cache: Using no cache (Run cache only).');
				$temp = new \Iresults\Core\Cache\Run();
				break;
		}

		/*
		 * Revert the tracelevel.
		 */
		$_GET['tracelevel'] = is_null($oldTraceLevel) ? $oldTraceLevel : -1;

		self::$instance = $temp;
		return $temp;
	}
	/**
	 * @see getSharedInstance()
	 */
	static public function makeInstance() {
		return static::getSharedInstance();
	}

	/**
	 * Removes the complete cache.
	 *
	 * @return	void
	 */
	abstract public function clear();

	/**
	 * If a second parameter is passed, the function sets the value $object at
	 * the key $key.
	 *
	 * If only one parameter is passed, getObjectForKey() is invoked with the
	 * given $key as argument.
	 *
	 * @param	string	$key
	 * @param	mixed	$object [optional]
	 * @return	mixed
	 */
	public function object($key, $object = NULL) {
		if (func_num_args() > 1) {
			$this->setObjectForKey($key, $object);
		}
		return $this->getObjectForKey($key);
	}

	/**
	 * Returns the value for the given key, if it exists, otherwise performs the
	 * closure given as the second argument.
	 *
	 * @param  string  	 $key        The property key to retrieve
	 * @param  \Closure  $closure    The closure to perform if the property isn't set
	 * @param  boolean 	 $saveResult If set to FALSE to result will not be stored using setObjectForKey()
	 * @return mixed				 Returns the value for the given key, or the result of the closure
	 */
	public function getObjectForKeyOrPerformClosure($key, $closure, $saveResult = TRUE) {
		$result = $this->getObjectForKey($key);
		if ($result === NULL) {
			$result = $closure($key);
			if ($saveResult) {
				$this->setObjectForKey($key, $result);
			}
		}
		return $result;
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* CACHE SCOPE    WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the language suffix to make the cache lanugage dependent.
	 *
	 * @return	string
	 */
	static public function _getLanguageSuffix() {
		if (!self::$_languageSuffix && self::$scope == self::SCOPE_LANGUAGE) {
			self::$_languageSuffix = strtoupper('_L' . \Iresults\Core\Iresults::getLanguage());
		}
		return self::$_languageSuffix;
	}

	/**
	 * Set the scope of the cache.
	 *
	 * @param	integer	$newScope The new scope as one of the SCOPE constants
	 * @return	void
	 */
	public function setScope($newScope) {
		self::$scope = $newScope;
	}

	/**
	 * Returns the scope of the cache.
	 *
	 * @return	integer    The current scope as one of the SCOPE constants
	 */
	public function getScope() {
		return self::$scope;
	}
}
