<?php

class Zend_Tool_Rpc_Endpoint_Cli extends Zend_Tool_Rpc_Endpoint_Abstract
{
    
    public static function main()
    {
        $cliEndpoint = new self();
        //$cliEndpoint->setDefaults();
        $cliEndpoint->handle();
    }
    
    protected function _init()
    {
        
    }
    
    protected function _preHandle()
    {
        
        
        $optParser = new Zend_Tool_Rpc_Endpoint_Cli_GetoptParser($this, $_SERVER['argv']);
        $optParser->parse();

        
    }
    
    protected function _postHandle()
    {
        echo $this->_response->getContent() . PHP_EOL;
    }
    
}
