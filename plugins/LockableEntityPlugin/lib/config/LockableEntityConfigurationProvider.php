<?php

interface LockableEntityConfigurationProvider
{
    /**
     *  All configurations MUST have static getter. In fact all of following methods
     *   could be considered 'static', but I avoid static methods due to php limitation.
     */
    public static function getInstance();
    
    /**
     *  Returns lock timeout in seconds
     */
    public function getLockTimeout();

    /**
     *  Returns internal URI to module which handles 'lock failed' message
     */
    public function getLockFailedRedirectUri();

    /**
     *  Returns message to use to display 'lock failed' on default template
     */
    public function getLockFailedMessage();

    /**
     *  Returns credential needed to delete any lock
     */
    public function getLockSupervisorCredential();
}

?>
