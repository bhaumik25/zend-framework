<?php

class Zend_Tool_Rpc_Manifest_ActionMetadata extends Zend_Tool_Rpc_Manifest_Metadata
{
    protected $_type = 'Action';
    protected $_actionName = null;

    public function setActionName($actionName)
    {
        $this->_actionName = $actionName;
        return;
    }
    
    public function getActionName()
    {
        return $this->_actionName;
    }
}