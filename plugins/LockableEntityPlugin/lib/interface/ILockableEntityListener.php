<?php

/**
 *  Public interface for listener class
 * 
 * @author sakfa
 */
interface ILockableEntityListener
{
    /**
     *  This method should connect event handler to symfony's event dispatching system,
     *   which will listen for changing of executed module/action.
     *  If ActionMatcher matches new module/action listener should acquire lock on entity
     *    using Locker instance.
     */
    public static function connect(
        sfEventDispatcher $dispatcher, 
        ILockableEntityActionMatcher $actionMatcher, 
        ILockableEntityLocker $locker
    );
}

?>
