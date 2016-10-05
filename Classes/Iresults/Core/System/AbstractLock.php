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
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_System
 */
abstract class AbstractLock
{
    /**
     * @var mixed The lock identifier.
     */
    protected $lockObject = null;

    /**
     * Returns an instance of the lock with the given object/variable as identifier.
     *
     * @param    mixed $lockObject The lock identifier
     */
    public function __construct($lockObject)
    {
        $this->lockObject = $lockObject;
    }

    /**
     * Attempts to acquire a lock. Blocking a thread's execution until the lock
     * can be acquired.
     *
     * @return    void
     */
    public function lock()
    {
        while ($this->_lockIsLocked()) {
            usleep(10);
        }
        $this->_lock();
    }

    /**
     * Attempts to acquire a lock and immediately returns if the attempt was
     * successful.
     *
     * @return    boolean    Returns if the lock could
     */
    public function tryLock()
    {
        if ($this->_lockIsLocked()) {
            return false;
        }

        $this->_lock();

        return true;
    }

    /**
     * Relinquishes a previously acquired lock.
     *
     * @return    void
     */
    public function unlock()
    {
        $this->_unlock();
    }

    /**
     * Locks a lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be acquired
     */
    abstract protected function _lock();

    /**
     * Relinquishes a previously acquired lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be relinquished
     */
    abstract protected function _unlock();

    /**
     * Determines if the lock exists. Only for internal use.
     *
     * @return    boolean    Returns if the lock could
     */
    abstract protected function _lockIsLocked();
}

