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
 * Ir adapter for the cache. Writes the cache to a file.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 */
class Ir extends \Iresults\Core\Cache\AbstractCache {
	/**
	 * @var array The cache itself.
	 */
	static protected $_cache = array();

	/**
	 * @var boolean Indicates if the shutdown handler was registered.
	 */
	static protected $_didInstallShutdownHandler = FALSE;

	/**
	 * @var string The file name of the cache file.
	 */
	static protected $_fileName = 'IRESULTS_CACHE';

	/**
	 * @var \Iresults\Core\System\LockAbstract A lock to provide errors if multiple
	 * processes wont the write the cache file.
	 */
	static protected $_lock = NULL;

	/**
	 * @var boolean Indicates if the cache was changed. If not, the cache file
	 * doesn't have to be written.
	 */
	static protected $_cacheWasChanged = FALSE;

	/**
	 * The constructor.
	 *
	 * @return	\Iresults\Core\Cache\Ir
	 */
	public function __construct() {
		if (!self::$_lock) {
			self::$_lock = new \Iresults\Core\System\Lock('ir_cache_lock' . self::_getLanguageSuffix());
		}

		parent::__construct();

		if (!self::$_didInstallShutdownHandler) {
			register_shutdown_function(array('\Iresults\Core\Cache\Ir', '_writeCacheFile'));
			self::$_didInstallShutdownHandler = TRUE;
		}
		if (!self::$_cache || empty(self::$_cache)) {
			self::_readCacheFile();
		}


		return $this;
	}

	/**
	 * Returns the object at the given key.
	 *
	 * @param	string	$key
	 * @return	mixed
	 */
	public function getObjectForKey($key) {
		if (isset(self::$_cache[$key])) {
			return self::$_cache[$key];
		} else {
			return NULL;
		}
	}

	/**
	 * Stores the value of $object at the given key.
	 *
	 * @param	string	$key
	 * @param	mixed	$object
	 * @return	void
	 */
	public function setObjectForKey($key, $object) {
		self::$_cacheWasChanged = TRUE;
		self::$_cache[$key] = $object;
	}

	/**
	 * Removes the object with the given key.
	 *
	 * @param	string	$key
	 * @return	void
	 */
	public function removeObjectForKey($key) {
		if (isset(self::$_cache[$key])) {
			unset(self::$_cache[$key]);
			self::$_cacheWasChanged = TRUE;
		}
	}

	/**
	 * Removes the complete cache.
	 *
	 * @return	void
	 */
	public function clear() {
		self::$_cache = array();
		$result = TRUE;
		$path = self::getCacheDir() . self::$_fileName . '*';
		$foundPaths = glob($path);

		if (!$foundPaths || empty($foundPaths)) {
			$this->pd('No matching cache files found for pattern "' . $path . '"');
			return;
		}

		self::$_lock->lock();
		foreach ($foundPaths as $onePath) {
			if (!unlink($onePath)) {
				$this->pd('Cache could not be cleared because the cache file $onePath couldn\'t be deleted');
				$result = FALSE;
			} else {
				$this->pd('Cache file "' . $onePath . '" deleted');
			}
		}
		self::$_lock->unlock();

		if ($result) {
			$this->pd('Cache cleared');
		}
	}

	/**
	 * If the object is destructed and uncommited changes exist, write the cache
	 * file.
	 *
	 * @return	void
	 */
	public function __destruct() {
		if (self::$_cacheWasChanged) {
			self::_writeCacheFile();
		}
	}

	/**
	 * Reads the cache from the cache file.
	 *
	 * @return	void
	 */
	static public function _readCacheFile() {
		$path = self::getCacheDir() . self::$_fileName . self::_getLanguageSuffix();
		if (!file_exists($path)) return;
		$contents = file_get_contents($path);
		if (!$contents) return;

		$temp = unserialize($contents);
		if ($temp === FALSE) return;
		self::$_cache = $temp;
	}

	/**
	 * Writes the cache to a cache file.
	 *
	 * @return	void
	 */
	static public function _writeCacheFile() {
		if (!self::$_cache || empty(self::$_cache)) {
			return;
		} else if (!self::$_cacheWasChanged) {
			\Iresults\Core\Iresults::pd('Cache was not changed');
			return;
		}
		$path = self::getCacheDir() . self::$_fileName . self::_getLanguageSuffix();
		$contents = serialize(self::$_cache);

		self::$_lock->tryLock();

		$fh = @fopen($path, 'wb');
		if (!$fh) {
			$msg = 'Couldn\'t open file "' . $path . '" for writing the cache information';
			trigger_error($msg, E_USER_WARNING);
			return;
		}

		if (fwrite($fh, $contents) === FALSE) {
			$msg = 'Writing cache to file "' . $path . '" failed';
			trigger_error($msg, E_USER_WARNING);
		}
		fclose($fh);

		self::$_lock->unlock();
		self::$_cacheWasChanged = FALSE;
	}

	/**
	 * Returns the path to the cache directory.
	 *
	 * @return	string
	 */
	static public function getCacheDir() {
		return \Iresults\Core\Iresults::getTempPath();
	}
}
