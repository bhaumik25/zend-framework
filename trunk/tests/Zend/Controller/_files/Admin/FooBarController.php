<?php
require_once 'Zend/Controller/Action.php';
require_once dirname(__FILE__) . '/../FooController.php';

class Admin_FooBarController extends FooController
{
    public function bazBatAction()
    {
        $this->_response->appendBody("Admin_FooBar::bazBat action called\n");
    }
}
