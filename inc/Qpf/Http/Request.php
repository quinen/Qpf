<?php

namespace Qpf\Http;

use Qpf\Core\Object;
use Qpf\Core\Data;

class Request extends Object {
    
    private $_superglobals = array(
        'server'    => array()
        ,'get'      => array()
        ,'post'     => array()
        ,'files'    => array()
        ,'request'  => array()
        ,'session'  => array()
        ,'env'      => array()
        ,'cookie'   => array()
    );
    
    private $_url       = array();
    
    public $controller  = "Index";
    
    public $action      = "index";
    
    public $params      = array();
    
    public function __construct(){        
        $this->_superglobals = array(
            'server'    => $_SERVER
            ,'get'      => $_GET
            ,'post'     => $_POST
            ,'files'    => $_FILES
            ,'request'  => $_REQUEST
            ,'session'  => array()//$_SESSION setted after start_session
            ,'env'      => $_ENV
            ,'cookie'   => $_COOKIE
        );
        
        $this->_initUrl();
    }
    
    public function __call($_method,$_args){
        if(in_array($_method,array_keys($this->_superglobals))){
            $nbArgs = count($_args);
            
            if($nbArgs<3){
                if($nbArgs>0){
                    // uppercase if server
                    $arg0 = (in_array($_method,array('server'))?strtoupper($_args[0]):$_args[0]);
                    if($nbArgs==1){
                        return Data::get($this->_superglobals[$_method],$arg0);
                    } else {
                        return Data::set($this->_superglobals[$_method],$arg0,$_args[1]);
                    }
                } else {
                    return Data::get($this->_superglobals[$_method]);
                }
            } else {
                $this->log("too many args");
                $this->log($_method);
                $this->log($_args);                
            }            
        }

    }
    
    private function _getRoute($_string){
        $array  = explode("/",$_string);
        $keys   = array('controller','action','params');
        $return = array();
        foreach($keys as $key){
            if($key!="params"){
                $value = array_shift($array);
            } else {
                $value = $array;
            }
            if(!empty($value)){
                $this->{$key}   = $value;
            }
            $return[$key] = $this->{$key};
        }
        return $return;
    }
    
    private function _initUrl(){
        $url    = array();
        
        // host
        $https  = (is_null($this->server('HTTPS'))?"":"s");
        $host   = "http".$https."://".$this->server('SERVER_NAME');

        // path
        $path   = substr($this->server('SCRIPT_FILENAME'),strlen($this->server('DOCUMENT_ROOT'))-1);
        $path   = substr($path,0,strpos($path,"pub/index.php"));

        // define web url to pub folder
        define('QPF_WWW_ROOT',$host.$path);        
        
        // uri
        $uri        = substr($this->server('REQUEST_URI'),strlen($path));
        $uriArray   = explode("?",$uri);        
        
        // route
        if(!is_null($this->server('PATH_INFO'))){
            $route = trim($this->server('PATH_INFO'),"/");
            
        } else {
            $route  = Data::get($uriArray,'0');
            $route  = trim($route,"index.php");
        }
        // mise en tableau de la route string
        $route      = $this->_getRoute($route);
        $this->_url    = compact('host','path','uri','route');
        
//Qpf::pre($this->_url);Qpf::pre($this->server());

    }
    
    public function url($_key = null,$_isFull = false){
        
        if(is_bool($_key)){
            $_isFull    = $_key;
            $_key       = null;
        }

        if(is_null($_key)){
            $_key = array('path','uri');
        } else if(is_string($_key)){
            $_key = explode("+",$_key);
        }
        
        if($_isFull){
            array_unshift($_key,'host');
        }
        
        if(in_array("route",$_key)){
            return array_intersect_key($this->_url,array_flip($_key));
        } else {
            return implode("",array_intersect_key($this->_url,array_flip($_key)));
        }
    }
}

