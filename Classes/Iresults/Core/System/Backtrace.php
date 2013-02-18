<?php
namespace Iresults\Core\System;

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
 * The iresults backtrace enables you to display a backtrace.
 * 
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_System
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
	 * Construtor for a backtrace object. The starting position in the backtrace
	 * stack may be passed as $startLevel. Also a maximum depth of the backtrace
	 * can be specified.
	 * 
	 * @param	integer	$startLevel The starting position in the backtrace stack
	 * @param	integer	$depthLevel The depth of the backtrace
	 * @return	\Iresults\Core\System\Backtrace
	 */
	public function __construct($startLevel = 2, $depthLevel = -1) {
		$this->startLevel = $startLevel;
		$this->depthLevel = $depthLevel;
		$this->backtrace = debug_backtrace();
		$this->time = time();
	}
	
	/**
	 * Renders the backtrace.
	 * 
	 * @return	string
	 */
	public function render() {
		$bt = $this->backtrace;
		$level = 0;
		$depth = $this->depthLevel;
		if ($depth == -1) $depth = count($bt);
		
		$result = 'Backtrace from ' . date('H:i:s', $this->time) . ' (current time: ' . date('H:i:s') . ')' . PHP_EOL;
		for($i = $this->startLevel; $i < $depth;$i++) {
			$levelArray = $bt[$i];
			$file = $this->_getLevelElement('file',$levelArray);
			$line = $this->_getLevelElement('line',$levelArray);
			$function = $this->_getLevelElement('function',$levelArray);
			$class = $this->_getLevelElement('class',$levelArray);
			$type = $this->_getLevelElement('type',$levelArray);
			$args = $this->_getLevelElement('args',$levelArray);
			$args = $this->_argsToString($args);
			
			if ($class) {
				$result .= "#$level: $class$type$function($args) \t\t called in $file @ $line" . PHP_EOL;
			} else {
				$result .= "#$level: $function($args) \t\t called in $file @ $line" . PHP_EOL;
			}
			
			$level++;
		}
		
		return $result;
	}
	
	/**
	 * Renders the backtrace.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return $this->render();
	}
	
	/**
	 * Returns the element of the backtrace level with the given key or an empty
	 * string if the key doesn't exist.
	 * 
	 * @param	string	$key        The element key
	 * @param	array & $levelArray Reference to the backtrace level array
	 * 
	 * @return	mixed
	 */
	protected function _getLevelElement($key, &$levelArray) {
		$value = '';
		if (is_array($levelArray) || (is_object($levelArray) && $levelArray instanceof ArrayAccess)) {
			if (isset($levelArray[$key])) {
				$value = $levelArray[$key];
			}
		}
		return $value;
	}
	
	/**
	 * Returns a string representation of the arguments array.
	 * 
	 * @param	array	$args The arguments array
	 * @return	string
	 */
	protected function _argsToString($args) {
		$result = array();
		foreach ($args as $index => $arg) {
			if (is_scalar($arg)) {
				$result[] = $arg;
			} else if (is_null($arg)) {
				$result[] = '(NULL)';
			} else if (is_array($arg)) {
				$result[] = 'Array('.count($arg).')';
			} else if (is_object($arg) && method_exists($arg, '__toString')) {
				try{
					$arg = '' . $arg;
				} catch(Exception $e) {
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
	 * @return	\Iresults\Core\System\Backtrace
	 */
	static public function makeInstance() {
		return new self();
	}
}
/*

'file' => '/var/www/vhosts/lieguide.li/httpdocs/src/lib/Smarty/Smarty.class.php',
    'line' => 1591,
    'function' => 'trigger_error',
    'class' => 'Smarty',
    'object' => 
    Smarty::__set_state(array(
      
      ),
       '_cache_include' => NULL,
       '_cache_including' => false,
    )),
    'type' => '->',
    'args' => 
    array (
      0 => 'unable to read resource: "admin_login.tpl"',
    ),
  ),
  
*/