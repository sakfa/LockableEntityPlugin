<?php

class frontendConfiguration extends sfApplicationConfiguration
{
  public function initialize() 
  {      
      LockableEntityListener::connect(
         $this->getEventDispatcher(), 
         new LockableEntityActionMatcher('test', 'edit'),
         new LockableEntityLocker('TestTable')
      );
  }
}
