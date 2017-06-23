<?php

namespace Iresults\Core\System;


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
     * @param mixed $lockObject The lock identifier
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

