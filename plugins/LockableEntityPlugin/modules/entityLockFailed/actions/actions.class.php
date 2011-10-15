<?php

/**
 *
 * @author sakfa
 */
class entityLockFailedActions extends sfActions
{
    protected $configuration;
    
    public function executeIndex()
    {
        $configuration = LockableEntitySfConfigConfiguration::getInstance();
    }
}
