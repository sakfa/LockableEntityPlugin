<?php

/**
 * This class listen to controller.change_action event to handle acquring locks
 *   on needed actions
 */
class LockableEntityListener implements ILockableEntityListener
{

    protected $locker;
    protected $actionMatcher;

    /**
     *  Connects LockableEntity behavior into event dispatcher.
     *  Pattern is either regexp (/regex/), plain string or single asterisk (meaning 'all')
     * 
     * @param string $modulePattern
     * @param string $actionPattern
     * @param LockableEntityLocker $locker Locker to use.
     */
    public static function connect(
        sfEventDispatcher $dispatcher, 
        ILockableEntityActionMatcher $actionMatcher,
        ILockableEntityLocker $locker)
    {
        $listener = new self($actionMatcher, $locker);
        $dispatcher->connect('controller.change_action', array($listener, 'onActionChanged'));
    }

    /**
     *  Initializes object.     
     * 
     * @param LockableEntityActionMatcher $actionMatcher
     * @param LockableEntityLocker $locker
     */
    protected function __construct(LockableEntityActionMatcher $actionMatcher, LockableEntityLocker $locker)
    {
        $this->actionMatcher = $actionMatcher;
        $this->locker = $locker;
    }

    /**
     *  Locks needed entity if user executes correct action.
     * 
     * @param sfEvent $event 
     */
    public function onActionChanged(sfEvent $event)
    {
        $parameters = $event->getParameters();
        if ($this->actionMatcher->actionMatches($parameters['module'], $parameters['action'])) {
            $this->locker->acquireLock();
        }
    }

}

