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
abstract class AbstractLock {
	/**
	 * @var mixed The lock identifier.
	 */
	protected $lockObject = NULL;
	
	/**
	 * Returns an instance of the lock with the given object/variable as identifier.
	 * 
	 * @param	mixed	$lockObject The lock identifier
	 * @return	\Iresults\Core\System\LockInterface
	 */
	public function __construct($lockObject) {
		$this->lockObject = $lockObject;
		return $this;
	}
	
	/**
	 * Attempts to acquire a lock. Blocking a thread's execution until the lock
	 * can be acquired.
	 * 
	 * @return	void
	 */
	public function lock() {
		while ($this->_lockIsLocked()) {
			usleep(10);
		}
		$this->_lock();
	}
	
	/**
	 * Attempts to acquire a lock and immediately returns if the attempt was
	 * successful.
	 * 
	 * @return	boolean    Returns if the lock could
	 */
	public function tryLock() {
		if ($this->_lockIsLocked()) return FALSE;
		
		$this->_lock();
		return TRUE;
	}
	
	/**
	 * Relinquishes a previously acquired lock.
	 * 
	 * @return	void
	 */
	public function unlock() {
		$this->_unlock();
	}
	
	/**
	 * Locks a lock. Only for internal use.
	 * @return	boolean	Returns if the lock could be acquired
	 */
	abstract protected function _lock();
	
	/**
	 * Relinquishes a previously acquired lock. Only for internal use.
	 * 
	 * @return	boolean	Returns if the lock could be relinquished
	 */
	abstract protected function _unlock();
	
	/**
	 * Determines if the lock exists. Only for internal use.
	 * 
	 * @return	boolean    Returns if the lock could
	 */
	abstract protected function _lockIsLocked();
}
	