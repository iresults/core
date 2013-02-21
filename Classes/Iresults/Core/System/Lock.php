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
