<?php

/**
 *  An lockable entity configuration which reads data from known keys from app.yml,
 *   and provides basic default values
 */
class LockableEntitySfConfigConfiguration implements LockableEntityConfigurationProvider
{
    public static function getInstance()
    {
        return new self();
    }
    
    public function getLockTimeout()
    {
        return sfConfig::get('app_LockableEntity_timeout', 15 * 60);
    }

    public function getLockFailedMessage()
    {
        return sfConfig::get('app_LockableEntity_lockFailedMessage', 'Entity is locked by %$1s to %$1s');
    }

    public function getLockFailedRedirectUri()
    {
        return sfConfig::get('app_LockableEntity_redirectUri', 'entityLockFailed/index');
    }

    public function getLockSupervisorCredential()
    {
        return sfConfig::get('app_LockableEntity_supervisorCredential', 'admin');
    }

}
