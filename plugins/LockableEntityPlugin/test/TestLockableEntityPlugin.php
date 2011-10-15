<?php

require_once(dirname(__FILE__).'/../lib/interface/ILockableEntityListener.php');
require_once(dirname(__FILE__).'/../lib/interface/ILockableEntityLocker.php');
require_once(dirname(__FILE__).'/../lib/interface/ILockableEntityActionMatcher.php');
require_once(dirname(__FILE__).'/../lib/LockableEntityListener.class.php');
require_once(dirname(__FILE__).'/../lib/LockableEntityLocker.class.php');
require_once(dirname(__FILE__).'/../lib/LockableEntityActionMatcher.class.php');

class TestLockableEntityPlugin extends PHPUnit_Framework_TestCase
{
    public function testPatternMatcher()
    {
        $matcher = new LockableEntityActionMatcher('*', '*');
        $this->assertTrue($matcher->actionMatches("anything", "anything"), "Asterisks are correctly matched");
        
        $matcher = new LockableEntityActionMatcher('module', 'action');
        $this->assertTrue($matcher->actionMatches('module', 'action'), "Plain test is correctly matched");
        $this->assertFalse($matcher->actionMatches('bad', 'action'), "Plain text is correctly not matched in module");
        $this->assertFalse($matcher->actionMatches('module', 'bad'), "Plain text is correctly not matched in action");
        
        $matcher = new LockableEntityActionMatcher('/^a/', '*');
        $this->assertTrue($matcher->actionMatches('abcde', 'anything'), "Module is correctly matched in regex");
        $this->assertFalse($matcher->actionMatches('bcde', 'anything'), "Module is correctly not matched in regex");
        
        $matcher = new LockableEntityActionMatcher('*', '/^a/');
        $this->assertTrue($matcher->actionMatches('anything', 'abcde'), "Action is correctly matched in regex");
        $this->assertFalse($matcher->actionMatches('anything', 'bcde'), "Action is correctly not matched in regex");        
    }
        
    public function testConnectListener()
    {             
        $dispatcher = new sfEventDispatcher();
        $matcher = new LockableEntityActionMatcher('match', '*');
        $locker = new LockableEntityLocker('TestModel', 'someKey');
        
        LockableEntityListener::connect($dispatcher, $matcher, $locker);
                        
        $this->assertEquals('controller.change_action', $dispatcher::$event, "Listener is bounded to correct event");
        $this->assertTrue(is_callable($dispatcher::$callback), "Dispatcher installed callable as callback");                
    }
    
    public function testLockSuccess()
    {
        $this->prepareEnvironment();
        
        $entity = new LockableTestModel();
        TestModelQuery::$model = $entity;
                
        $controller = sfContext::getInstance()->getController();        
        call_user_func(sfEventDispatcher::$callback, new sfEvent(array('module'=>'match','action'=>'whatever')));
        
        $this->assertEquals("login", $entity->lockedTo, "Entity has been locked to current user");
        $this->assertNull($controller->redirectedTo, "Controller has not redirected");
    }
    
    public function testLockFailed()
    {
        $entity = new NotLockableTestModel();
        TestModelQuery::$model = $entity;
        
        $controller = sfContext::getInstance()->getController();        
        call_user_func(sfEventDispatcher::$callback, new sfEvent(array('module'=>'match','action'=>'whatever')));
        
        $this->assertNull($entity->lockedTo, "Entity has not been locked to current user");
        $this->assertNotNull($controller->redirectedTo, "Controller has redirected");
    }
    
    
    protected function prepareEnvironment()
    {
        $user = new UserMock();
        $user->setLogin('login');
        $controller = new ControllerMock();
        $context = new sfContext(new RequestStub(), $user, $controller);
        sfContext::$instance = $context;
        
        $dispatcher = new sfEventDispatcher();
        $matcher = new LockableEntityActionMatcher('match', '*');
        $locker = new LockableEntityLocker('TestModel', 'someKey');
        
        LockableEntityListener::connect($dispatcher, $matcher, $locker);
    }
}



// million of stubs and mocks, needed for all this stuff to work
// sorry for that, but aop needs half of symfony context to work and in this case some propel model are needed
class LockableTestModel
{
    public $lockedTo;
    public function lockTo($user)
    {
        $this->lockedTo = $user;
        return true;
    }
}
class NotLockableTestModel
{
    public $lockedTo;
    public function lockTo($user)
    {
        $this->lockedTo = null;
        return false;
    }
}

class TestModelQuery
{
    public static $model;
    public function findPk($pk)
    {
        return self::$model;
    }
}

class sfEvent
{
    public function __construct($params) {
        $this->params = $params;
    }
    
    public function getParameters()
    {
        return $this->params;
    }
}

class RequestStub
{
    public function getParameter()
    {
        return 1;
    }
}

class UserMock
{
    protected $login;
    public function getLogin() {
        return $this->login;
    }

    public function setLogin($login) {
        $this->login = $login;
    }
}
    
class ControllerMock
{
    public $redirectedTo;
    public function redirect($uri) {
        $this->redirectedTo = $uri;
    }
}

class sfContext
{
    public static $instance;
    public static function getInstance() {
        return self::$instance;
    }
    
    public function __construct($request, $user, $controller) {
        $this->request = $request;
        $this->user = $user;
        $this->controller = $controller;
    }
    
    public function getRequest() {
        return $this->request;
    }
    public function getUser() {
        return $this->user;
    }
    public function getController() {
        return $this->controller;
    }
}

class sfEventDispatcher
{
    public static $event, $callback;
    public static function connect($event, $callback)
    {
        self::$event = $event;
        self::$callback = $callback;
    }
}
