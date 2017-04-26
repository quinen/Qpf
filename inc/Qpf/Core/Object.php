<?php

namespace Qpf\Core;

use \Psr\Log\LoggerAwareInterface;
use \Psr\Log\LoggerInterface;
use \Psr\Log\LogLevel;
use \Psr\Log\NullLogger;

class Object implements LoggerAwareInterface
{
    
    private $_logger = null;
    
    public function log($_msg = "",$_opt=array())
    {
        if(is_string($_opt)){
            $_opt =array('type'=>$_opt,'trace'=>false);
        }
        
        $opt_defaults = array(
            'level'     => LogLevel::DEBUG
            ,'logger'   => "\Qpf\Logger\File"
            ,'color'    => false
        );
        $opt = array_merge($opt_defaults,$_opt);
        $opt['trace']=true;
        
        // make sure is a string
        if(is_string($_msg)){
            $msg = $_msg;
        } else {
            $msg = var_export($_msg,true);
        }
        
        // set logger
        if(!$this->_logger){
            $loggerName = $opt['logger']."Logger";
            $loggerClass = new $loggerName;
            $this->setLogger($loggerClass);
        }
        
        // set level
        $level = $opt['level'];
        
        // set color
        if($opt['color']){
            $msg = (new String($msg))->shellColor($opt['color']);
        }
        
        // on elimine les clés de la fonction
        $opt = array_diff_key($opt,$opt_defaults);
        $this->_logger->log($level,$msg,$opt);
        
    }

    //public function setLogger(LoggerInterface $logger);
    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }
}