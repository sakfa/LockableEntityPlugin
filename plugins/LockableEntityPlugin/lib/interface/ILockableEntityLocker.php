<?php

/**
 * Public interface of locker class.
 *
 * @author sakfa
 */
interface ILockableEntityLocker
{
    /**
     *  This method should acquire lock on entity and handle locking failure.
     */
    public function acquireLock();
}

?>
