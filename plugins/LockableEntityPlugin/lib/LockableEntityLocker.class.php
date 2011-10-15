<?php

/**
 * This class performs actual locking of entity and handles lock failure.
 *
 * @FIXME this class is the only one in package that directly depends on sfContext.
 *   we cannot pass any of context instances (request, controller and user is needed) in
 *   application configuration, because it's executed before sfContext::createInstance() call :/
 *  To workaround issue context-dependent methods are as short as possible - you can always subclass
 *   locker to eliminate context dependency.
 * 
 *  Note that this class is the only one that needs to know current user's login.
 *  
 * @author sakfa
 */
class LockableEntityLocker implements ILockableEntityLocker
{

    protected $modelName;
    protected $primaryKeyName;

    /**
     *
     * @param string $modelName A name of a model with lockable_entity behavior
     * @param string $primaryKeyName A name of a request parameter, with pk
     */
    public function __construct($modelName, $primaryKeyName = 'id')
    {
        $this->modelName = $modelName;
        $this->primaryKeyName = $primaryKeyName;
    }

    /**
     *  Locks entity for given user.
     * 
     * @return True on success.
     */
    public function acquireLock()
    {
        $entity = $this->getEntityInstance();

        if (!$entity->lockTo($this->getCurrentUserLogin())) {
            $this->handleFailedLock();
        }
    }

    /**
     *  Called if locking failed.
     */
    protected function handleFailedLock()
    {
        $this->redirect('entityLockFailed/index');
    }

    /**
     *  Gets an instance of entity, on which we must acquire lock.
     */
    protected function getEntityInstance()
    {
        $queryClass = $this->modelName . 'Query';
        $query = new $queryClass;
        $pk = $this->getEntityPrimaryKey();

        return $query->findPk($pk);
    }

    /**
     *  Gets current user login.
     */
    protected function getCurrentUserLogin()
    {
        return sfContext::getInstance()->getUser()->getLogin();
    }

    /**
     *  Redirects current action and stops execution.
     * @param type $internalUri 
     */
    protected function redirect($internalUri)
    {
        sfContext::getInstance()->getController()->redirect($internalUri);
        //no more code execution here
    }

    /**
     *  Gets entities primary key from request. 
     */
    protected function getEntityPrimaryKey()
    {
        return sfContext::getInstance()->getRequest()->getParameter($this->primaryKeyName);
    }

}

