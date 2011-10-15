<?php

class myUser extends sfBasicSecurityUser
{
    public function getLogin()
    {
        return $this->getAttribute('login');
    }
    
    public function setLogin($login)
    {
        $this->setAttribute('login', $login);
    }
   
}
