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


/**
 * The Cookie Manager provides functionality to retrieve, add and remove cookie
 * values.
 *
 * The cookie names will be prepended with a prefix, taken from the cookieScopePrefix
 * property, to ensure that only cookies owned by the Manager are manipulated.
 *
 * WARNING: The cookie expiration dates are calculated according to the servers
 * time zone.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 */
class CookieManager extends \Iresults\Core\Singleton implements \Iresults\Core\KVCInterface {
	/**
	 * The URL value key to pass to reset the cookies.
	 */
	const CLEAR_COOKIES = 'iresults_cookie_clear_cookies';
	/**
	 * The default cookie lifetime
	 *
	 * If the value is 0, the cookie will expire at the end of the session (when the
	 * browser is closed).
	 *
	 * @var integer
	 */
	protected $defaultLifetime = 0;
	/**
	 * The prefix to define the cookies scope
	 *
	 * @var string
	 */
	protected $cookieScopePrefix = '__iresults_cookie_';
	/**
	 * The internal storage for the cookies
	 *
	 * @var array<mixed>
	 */
	protected $cookies = NULL;
	/**
	 * The constructor
	 */
	public function __construct() {
		$this->cookies = $_COOKIE;
		if (isset($_GET[self::CLEAR_COOKIES]) && $_GET[self::CLEAR_COOKIES]) {
			$this->clear();
			\Iresults\Core\Iresults::pd('Cookies cleared');
		}
		return $this;
	}

	/**
	 * Returns if an object for the given key exists.
	 *
	 * @param	string	$key
	 * @return	boolean			Returns TRUE if a cookie for key exists, otherwise FALSE
	 */
	public function hasObjectForKey($key) {
		$key = $this->cookieScopePrefix . $key;
		return isset($this->cookies[$key]);
	}

	/**
	 * Returns the object at the given key.
	 *
	 * @param	string	$key
	 * @return	mixed
	 */
	public function getObjectForKey($key) {
		$key = $this->cookieScopePrefix . $key;
		if (isset($this->cookies[$key])) {
			return $this->cookies[$key];
		}
		return NULL;
	}

	/**
	 * Stores the value of $object at the given key.
	 *
	 * @param	string	$key
	 * @param	mixed	$object
	 * @return	void
	 */
	public function setObjectForKey($key, $object) {
		$this->setObjectForKeyWithLifeTimePathAndDomain($key, $object, '_');
	}

	/**
	 * Stores the value of $object at the given key, for the defined lifetime.
	 *
	 * @param	string	$key
	 * @param	mixed	$object
	 * @param 	integer $lifetime 	The cookie lifetime in seconds
	 * @return	void
	 */
	public function setObjectForKeyWithLifeTime($key, $object, $lifetime) {
		$this->setObjectForKeyWithLifeTimePathAndDomain($key, $object, $lifetime);
	}

	/**
	 * Store the value of object at the given key for the defined lifetime, settings the
	 * path, domain, secure-flag and httponly-flag.
	 *
	 * @param string  	$key      The key to use
	 * @param mixed  	$object   The value to store
	 * @param integer  	$lifetime The cookie lifetime in seconds from now
	 * @param string  	$path     The path on the server in which the cookie will be available
	 * @param string  	$domain   The domain that the cookie is available to (defaults to the server domain)
	 * @param boolean 	$secure   Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client
	 * @param boolean 	$httponly When TRUE the cookie will be made accessible only through the HTTP protocol
	 * @return void
	 */
	public function setObjectForKeyWithLifeTimePathAndDomain($key, $object, $lifetime = '_', $path = '/', $domain = '', $secure = FALSE, $httponly = FALSE) {
		$key = $this->cookieScopePrefix . $key;
		$expires = $this->defaultLifetime;
		if ($lifetime !== '_') {
			$expires = time() + $lifetime;
		}
		// Transform the boolean into an integer
		if (is_bool($object)) {
			$object = intval($object);
		} else if (is_object($object)) {
			$object = '' . $object;
		}
		$this->cookies[$key] = $object;
		if (!setcookie($key, $object, $expires, $path, $domain, $secure, $httponly)) {
			\Iresults\Core\Iresults::pd('Could not set cookie for key "' . $key . '"', $object);
		}
	}

	/**
	 * Removes the object with the given key.
	 *
	 * @param	string	$key
	 * @return	void
	 */
	public function removeObjectForKey($key) {
		$this->removeObjectForKeyWithLifeTimePathAndDomain($key);
	}

	/**
	 * Remove the value of object at the given key, for the path, domain, secure-flag and httponly-flag.
	 *
	 * You have to remove a cookies, with the same parameters as you created it.
	 *
	 * @param string  	$key      The key to use
	 * @param mixed  	$object   The value to store
	 * @param string  	$path     The path on the server in which the cookie will be available
	 * @param string  	$domain   The domain that the cookie is available to (defaults to the server domain)
	 * @param boolean 	$secure   Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client
	 * @param boolean 	$httponly When TRUE the cookie will be made accessible only through the HTTP protocol
	 * @return void
	 */
	public function removeObjectForKeyWithLifeTimePathAndDomain($key, $path = '/', $domain = '', $secure = FALSE, $httponly = FALSE) {
		$key = $this->cookieScopePrefix . $key;
		$expires = time() - 30758400; // One year in seconds (60 * 60 * 24 * 356)

		setcookie($key, '', $expires, $path, $domain, $secure, $httponly);
		unset($this->cookies[$key]);
	}

	/**
	 * Removes the cookies in the managed scope.
	 *
	 * @return	void
	 */
	public function clear() {
		$cookieScopePrefix = $this->cookieScopePrefix;
		$cookieScopePrefixLength = strlen($cookieScopePrefix);
		foreach ($this->cookies as $key => $value) {
			if (substr($key, 0, $cookieScopePrefixLength) === $cookieScopePrefix) {
				$this->removeObjectForKey($key);
			}
		}
	}

	/**
     * If only one parameter is passed, getObjectForKey() is invoked with the
     * given $key as argument.
	 * If a second parameter is passed, the function sets the value $object at
	 * the key $key.
	 *
	 * @param	string	$key
	 * @param	mixed	$object
	 * @return	mixed
	 */
	public function object($key, $object = NULL) {
		if (func_num_args() > 1) {
			$this->setObjectForKey($key, $object);
		}
		return $this->getObjectForKey($key);
	}
}