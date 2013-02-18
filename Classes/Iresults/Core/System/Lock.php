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
 * A lock which is checked through existence of a file.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_System
 */
class Lock extends \Iresults\Core\System\AbstractLock implements \Iresults\Core\System\LockInterface {
	/**
	 * Locks a lock. Only for internal use.
	 * @return	boolean	Returns if the lock could be acquired
	 */
	protected function _lock() {
		return touch($this->_getLockPath());
	}

	/**
	 * Relinquishes a previously acquired lock. Only for internal use.
	 *
	 * @return	boolean	Returns if the lock could be relinquished
	 */
	protected function _unlock() {
		return unlink($this->_getLockPath());
	}

	/**
	 * Determines if the lock exists. Only for internal use.
	 *
	 * @return	boolean    Returns if the lock could
	 */
	protected function _lockIsLocked() {
		return file_exists($this->_getLockPath());
	}

	/**
	 * Returns the path to the lock file.
	 *
	 * @return	string
	 */
	protected function _getLockPath() {
		$name = strtoupper('' . $this->lockObject);
		return \Iresults\Core\Iresults::getTempPath() . '/ir_lock_' . $name . '.lock';
	}
}
