<?php

namespace Iresults\Core\System;


/**
 * The interface of lock implementations.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_System
 */
interface LockInterface
{
    /**
     * Attempts to acquire a lock. Blocking a thread's execution until the lock
     * can be acquired.
     *
     * @return    void
     */
    public function lock();

    /**
     * Attempts to acquire a lock and immediately returns if the attempt was
     * successful.
     *
     * @return    boolean    Returns if the lock could be acquired
     */
    public function tryLock();

    /**
     * Relinquishes a previously acquired lock.
     *
     * @return    void
     */
    public function unlock();
}

