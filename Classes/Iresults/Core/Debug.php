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
 * Prints a debug output.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults
 */
class Debug {
	/**
	 * @var integer The maximum depth level in the object tree.
	 */
	protected $_maxLevel = 25;

	/**
	 * @var integer The current depth level in the object tree.
	 */
	protected $_currentLevel = 0;

	/**
	 * @var array<mixed> A storage for objects that already have been processed.
	 */
	protected $_storage = NULL;

	/**
	 * @var string The output.
	 */
	private $_output = '';
	/**
	 * Indicates the environment
	 *
	 * @var boolean
	 */
	protected $_isWebEnvironment = -1;

	/**
	 * The output shown when the maximum (depth) level is reached.
	 */
	const MAX_LEVEL_REACHED_MESSAGE = '(ML)';


	/**
	 * The constructor takes the object to debug as it's argument.
	 *
	 * @param	mixed	$object
	 * @return	\Iresults\Core\Debug
	 */
	public function __construct($object = NULL) {
		/*
		 * @Info: The object argument must be optional because Flow throws an
		 * exception if isset(arguments[0]) evaluates to FALSE.
		 */

		$this->_storage = array();
		$this->_output = '';
		$this->debug($object);

		unset($this->_storage);
		$this->_storage = NULL;

		return $this;
	}

	/**
	 * Debugs a variable.
	 *
	 * @param	mixed	$object 				The object/variable to debug
	 * @param 	boolean	$tempIsWebEnvironment 	Temporarily overwrite the isWebEnvironment configuration
	 * @return	void
	 */
	public function debug($object, $tempIsWebEnvironment = -1) {
		$hash = '';
		$objectId = '';
		$oldIsWebEnvironment = -1;
		static $printHash = TRUE;

		// Check if the deepest level is reached.
		if ($this->_currentLevel >= $this->_maxLevel) {
			$this->_add(self::MAX_LEVEL_REACHED_MESSAGE);
			return;
		}
		$this->_currentLevel = $this->_currentLevel + 1;


		/*
		 * Check the storage if the object does already exist.
		 */
		if (is_object($object)) {
			$hash = spl_object_hash($object);

			if (isset($this->_storage[$hash])) {
				$this->_add('Recursion for object '. get_class($object) . ' #' . $hash);
				$this->_currentLevel = $this->_currentLevel - 1;
				return;
			}

			$this->_storage[$hash] = $object;
			if ($printHash) {
				$objectId = '#' . $hash . ' ';
			}

		}

		// Check the environment
		if ($this->_isWebEnvironment === -1) {
			$this->_isWebEnvironment = (\Iresults\Core\Iresults::getEnvironment() === \Iresults\Core\Iresults::ENVIRONMENT_WEB);
		}
		if ($tempIsWebEnvironment !== -1) {
			$oldIsWebEnvironment = $this->_isWebEnvironment;
			$this->_isWebEnvironment = $tempIsWebEnvironment;
		}

		/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		if (is_null($object)) {
			$this->_add('NULL');
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (is_bool($object)) {
			$msg = '(bool) TRUE';
			if (!$object) $msg = '(bool) FALSE';
			$this->_add($msg);
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (is_string($object)) {
			if ($this->_isWebEnvironment) {
				$this->_add('(string) "' . htmlspecialchars( $object ) . '"');
			} else {
				$this->_add('(string) "' . $object . '"');
			}

			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (is_int($object)) {
			$this->_add("(int) $object");
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (is_float($object)) {
			$this->_add("(float) $object");
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (is_array($object)) {
			$this->_add('array (' . count($object) . ') => {');
			$this->_currentLevel = $this->_currentLevel + 1;
			foreach ($object as $key => $element) {
				$this->_add("$key => ");
				$this->debug($element);
			}
			$this->_currentLevel = $this->_currentLevel - 1;
			$this->_add('}');
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (get_class($object) == '\Iresults\Core\Nil') {
			$this->_add('nil');
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if ($object instanceof \Traversable) {
			$this->_add(get_class($object) . ' (' . $objectId . count($object) . ') => {');
			$this->_currentLevel = $this->_currentLevel + 1;
			foreach ($object as $key => $element) {
				$this->_add("$key => ",false);
				$this->debug($element);
			}
			$this->_currentLevel = $this->_currentLevel - 1;
			$this->_add('}');
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if ($object instanceof \DateTime) {
			$dateTime = var_export($object,TRUE);
			$dateTime = str_replace('::__set_state(array(',' => {',$dateTime);
			$dateTime = str_replace('))','}',$dateTime);
			$dateTime = str_replace(array(",\n",",\r",",\r\n"), PHP_EOL, $dateTime);
			$dateTime = get_class($object) . ' => {' . $object->format('r') . '}';
			$this->_add($dateTime);
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if ($object instanceof \Iresults\Core\DateTime) {
			$dateTime = var_export($object->getRaw(),TRUE);
			$dateTime = str_replace('::__set_state(array(',' => {',$dateTime);
			$dateTime = str_replace('))','}',$dateTime);
			$dateTime = str_replace(array(",\n",",\r",",\r\n"), PHP_EOL, $dateTime);
			$this->_add($dateTime);
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (is_a($object,'Tx_Extbase_Error_Message')) {
			$this->_add(get_class($object) . " => $object");
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (method_exists($object,'_getProperties')) {
			$properties = $object->_getProperties();
			$this->_add(get_class($object) . $objectId . ' => {');
			$this->_currentLevel = $this->_currentLevel + 1;
			foreach ($properties as $key => $element) {
				$this->_add("$key => ",false);
				$this->debug($element);
			}
			$this->_currentLevel = $this->_currentLevel - 1;
			$this->_add('}');
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (is_a($object, 'tslib_cObj')) {
			$properties = $object->data;
			$this->_add(get_class($object) . $objectId . ' => {');
			$this->_currentLevel = $this->_currentLevel + 1;
			foreach ($properties as $key => $element) {
				$this->_add("$key => ",false);
				$this->debug($element);
			}
			$this->_currentLevel = $this->_currentLevel - 1;
			$this->_add('}');
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (method_exists($object,'getData')) {
			$properties = $object->getData();
			$this->_add(get_class($object) . $objectId . ' => {');
			$this->_currentLevel = $this->_currentLevel + 1;
			foreach ($properties as $key => $element) {
				$this->_add("$key => ",false);
				$this->debug($element);
			}
			$this->_currentLevel = $this->_currentLevel - 1;
			$this->_add('}');
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (is_a($object,'\Iresults\Core\Value')) {
			$property = $object->getValue();
			$this->_add(get_class($object) . $objectId . ' => {');
			$this->_currentLevel = $this->_currentLevel + 1;
			$this->debug($property);
			$this->_currentLevel = $this->_currentLevel - 1;
			$this->_add('}');
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (is_a($object, '\Iresults\Core\System\FilesystemInterface')) {
			$this->_add(get_class($object) . $objectId . ' => {');
			$this->_currentLevel = $this->_currentLevel + 1;
			$this->_add('path => "' . $object->getPath() . '"');
			$this->_currentLevel = $this->_currentLevel - 1;
			$this->_add('}');
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if ($object instanceof \Exception) {
			$this->_add(get_class($object) . $objectId . ' => {');
			$this->_currentLevel = $this->_currentLevel + 1;
			$this->_add($object->getCode() . ': ' . $object->getMessage());
			$this->_currentLevel = $this->_currentLevel - 1;
			$this->_add('}');
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (is_resource($object)) {
			$this->_add('(resource) ' . get_resource_type($object) . ' ' . substr('' . $object, 9));
			/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
		} else if (is_object($object)) {
			$properties = get_object_vars($object);
			$this->_add(get_class($object) . $objectId . ' => {');
			$this->_currentLevel = $this->_currentLevel + 1;
			foreach ($properties as $key => $element) {
				$this->_add("$key => ",false);
				$this->debug($element);
			}
			$this->_currentLevel = $this->_currentLevel - 1;
			$this->_add('}');
		}

		$this->_currentLevel = $this->_currentLevel - 1;
		if ($tempIsWebEnvironment !== -1) {
			$this->_isWebEnvironment = $oldIsWebEnvironment;
		}
	}

	/**
	 * Adds a text to the output.
	 *
	 * @param	string	$text
	 * @param	boolean	$break
	 * @return	void
	 */
	protected function _add($text, $break = TRUE) {
		// Check the environment and print &nbsp; for web and \t for a shell
		if ($this->_isWebEnvironment === -1) {
			$this->_isWebEnvironment = (\Iresults\Core\Iresults::getEnvironment() === \Iresults\Core\Iresults::ENVIRONMENT_WEB);
		}
		for($i = 1; $i < $this->_currentLevel; $i++) {
			if ($this->_isWebEnvironment) {
				$this->_output .= '&nbsp;&nbsp;&nbsp;&nbsp;';
			} else {
				//$this->_output .= "\t";
				$this->_output .= '   ';
			}
		}
		$this->_output .= "$text" . PHP_EOL;
	}

	/**
	 * Returns the output.
	 *
	 * @return	string
	 */
	protected function _get() {
		return $this->_output;
	}

	/**
	 * Returns the debug output.
	 *
	 * @return	string
	 */
	public function get() {
		return $this->_get();
	}
	public function __toString() {
		return $this->_get();
	}

	/**
	 * Returns the environment setting
	 *
	 * @return boolean
	 */
	public function getIsWebEnvironment() {
	    return $this->_isWebEnvironment;
	}

	/**
	 * Overwrite the environment setting
	 *
	 * @param  boolean $isWebEnvironment
	 * @return boolean						Returns the original value
	 */
	public function setIsWebEnvironment($isWebEnvironment) {
		$oldIsWebEnvironment = $this->_isWebEnvironment;
	    $this->_isWebEnvironment = $isWebEnvironment;
	    return $oldIsWebEnvironment;
	}

	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the shared instance.
	 *
	 * @return \Iresults\Core\Debug
	 */
	static public function sharedInstance() {
		static $sharedInstance = NULL;
		if (!$sharedInstance) {
			$sharedInstance = new self();
		}
		return $sharedInstance;
	}
}
