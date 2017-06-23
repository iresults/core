<?php

namespace Iresults\Core\System;


/**
 * A lock which is checked through existence of a file.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_System
 */
class Lock extends \Iresults\Core\System\AbstractLock implements \Iresults\Core\System\LockInterface
{
    /**
     * Locks a lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be acquired
     */
    protected function _lock()
    {
        return touch($this->_getLockPath());
    }

    /**
     * Relinquishes a previously acquired lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be relinquished
     */
    protected function _unlock()
    {
        return unlink($this->_getLockPath());
    }

    /**
     * Determines if the lock exists. Only for internal use.
     *
     * @return    boolean    Returns if the lock could
     */
    protected function _lockIsLocked()
    {
        return file_exists($this->_getLockPath());
    }

    /**
     * Returns the path to the lock file.
     *
     * @return    string
     */
    protected function _getLockPath()
    {
        $name = strtoupper('' . $this->lockObject);

        return \Iresults\Core\Iresults::getTempPath() . '/ir_lock_' . $name . '.lock';
    }
}
