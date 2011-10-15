<?php

/**
 * default actions.
 *
 * @package    aop_test
 * @subpackage default
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $this->forward('test', 'index');
    }

    public function executeLogin(sfWebRequest $request) {
        if ($request->isMethod(sfWebRequest::POST)) {
            $user = UserQuery::create()->findPk($request->getParameter('login'));            
            $this->getUser()->setLogin($user ? $user->getLogin() : null);
        }
        
        $this->redirect('test/index');
    }

}
