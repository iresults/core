<?php
namespace Iresults\Core\System;

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
use Iresults\Core\Iresults;


/**
 * The iresults backtrace enables you to save a backtrace snapshot and display
 * it later
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_System
 */
class Backtrace {
	/**
	 * @var integer The starting position in the backtrace stack.
	 */
	protected $startLevel = 0;

	/**
	 * @var integer The depth of the backtrace.
	 */
	protected $depthLevel = -1;

	/**
	 * The backtrace.
	 *
	 * @var array<array<string>>
	 */
	protected $backtrace = NULL;

	/**
	 * The time the exception has been thrown.
	 *
	 * @var integer
	 */
	protected $time;

	/**
	 * Constructor for a backtrace object
	 *
	 * The starting position in the backtrace stack may be passed as $startLevel.
	 * Also a maximum depth of the backtrace can be specified.
	 *
	 * @param    integer $startLevel The starting position in the backtrace stack
	 * @param    integer $depthLevel The depth of the backtrace
	 * @return    \Iresults\Core\System\Backtrace
	 */
	public function __construct($startLevel = 2, $depthLevel = -1) {
		$options = FALSE;
		if ($depthLevel !== -1 && version_compare(PHP_VERSION, '5.4.0') >= 0) {
			$backtrace = debug_backtrace($options, 10);
		} else {
			$backtrace = debug_backtrace($options);
		}

		$this->startLevel = $startLevel;
		$this->depthLevel = $depthLevel;
		$this->backtrace = $backtrace;
		$this->time = time();
	}

	/**
	 * Renders the backtrace.
	 *
	 * @return    string
	 */
	public function render() {
		$backtrace = $this->backtrace;
		$level = 0;
		$depth = $this->depthLevel;
		if ($depth == -1) $depth = count($backtrace);

		$result = 'Backtrace from ' . date('H:i:s', $this->time) . ' (current time: ' . date('H:i:s') . ')' . PHP_EOL;
		for ($i = $this->startLevel; $i < $depth; $i++) {
			$levelArray = $backtrace[$i];
			$file = $this->_getLevelElement('file', $levelArray);
			$line = $this->_getLevelElement('line', $levelArray);
			$function = $this->_getLevelElement('function', $levelArray);
			$class = $this->_getLevelElement('class', $levelArray);
			$type = $this->_getLevelElement('type', $levelArray);
			$args = $this->_getLevelElement('args', $levelArray);
			$args = $this->_argsToString($args);

			if ($class) {
				$result .= "#$level: $class$type$function($args) ";
			} else {
				$result .= "#$level: $function($args) ";
			}

			if (Iresults::getEnvironment() === Iresults::ENVIRONMENT_WEB) {
				$result .= "\t\t called in <a href='file://$file' target='_blank'>$file</a> @ $line" . PHP_EOL;
			} else {
				$result .= "\t\t called in $file @ $line" . PHP_EOL;
			}

			$level++;
		}
		return $result;
	}

	/**
	 * Renders the backtrace.
	 *
	 * @return    string
	 */
	public function __toString() {
		return $this->render();
	}

	/**
	 * Returns the element of the backtrace level with the given key or an empty
	 * string if the key doesn't exist.
	 *
	 * @param    string $key          The element key
	 * @param    array  & $levelArray Reference to the backtrace level array
	 *
	 * @return    mixed
	 */
	protected function _getLevelElement($key, &$levelArray) {
		$value = '';
		if (is_array($levelArray) || (is_object($levelArray) && $levelArray instanceof \ArrayAccess)) {
			if (isset($levelArray[$key])) {
				$value = $levelArray[$key];
			}
		}
		return $value;
	}

	/**
	 * Returns a string representation of the arguments array.
	 *
	 * @param    array $args The arguments array
	 * @return    string
	 */
	protected function _argsToString($args) {
		$result = array();
		foreach ($args as $arg) {
			if (is_scalar($arg)) {
				$result[] = $arg;
			} else if (is_null($arg)) {
				$result[] = '(NULL)';
			} else if (is_array($arg)) {
				$result[] = 'Array(' . count($arg) . ')';
			} else if (is_object($arg) && method_exists($arg, '__toString')) {
				try {
					$arg = '' . $arg;
				} catch (\Exception $e) {
					$arg = 'No argument info (' . $e->getMessage() . ')';
				}
				$result[] = $arg;
			} else if (is_object($arg) && method_exists($arg, 'description')) {
				$description = $arg->description();
				if (is_scalar($description)) {
					$result[] = $description;
				}
			} else if (is_object($arg)) {
				$result[] = get_class($arg);
			}
		}
		return implode(', ', $result);
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Factory method: Returns a new Backtrace instance.
	 *
	 * @return    \Iresults\Core\System\Backtrace
	 */
	static public function makeInstance() {
		return new static();
	}
}