<?php

namespace Qpf\Controller;

use Qpf\Core\Data;
use Qpf\Core\Exception;
use Qpf\Core\Object;
use Qpf\View\View;

abstract class Controller extends Object{
    
    private $_vars      = array();
    
    public $action      = null;    
    public $controller  = null;    
    public $request     = null;    
    public $view        = null;
    public $layout      = "default";
    public $theme       = "default";
    
    public $viewExt     = array();
    

    public function __construct(){
        // Détermination du nom du fichier vue à partir du nom du contrôleur actuel
        $controllerClass    = get_class($this);
        $aSlash             = strrpos($controllerClass,"\\")+1;
        $this->controller   = substr($controllerClass,$aSlash,strrpos($controllerClass,"Controller")-$aSlash);
        $this->init();
    }
    
    public function init(){}
    public function beforeAction(){}
    public function beforeRender(){}
    
    protected function _generateView()
    {
        $View   = new View($this->view, $this);
        $vars   = $this->get();
        $View->generate($vars);
    }
    
    public function get($_key=null)
    {
        return Data::get($this->_vars,$_key);
    }
    
    protected function set($_key,$_value=null){
        if(is_array($_key)){
            $this->_vars = array_merge($this->_vars,$_key);    
        } else {
            $this->_vars = Data::set($this->_vars,$_key,$_value);
        }
        return $this->_vars;
    }    
    
    public function runAction($_action,$_params){
        if(method_exists($this,$_action)){
            $this->action    = $_action;
            $this->view      = $_action;
            
            $this->beforeAction();
            call_user_func_array(array($this,$_action),$_params);
            
            if($this->view){
                $this->beforeRender();
                $this->_generateView();
            }
            
        } else {
            $controllerClass = get_class($this);
            throw new Exception("Action '".$_action."' non définie dans la classe ".$controllerClass."");
        }
    }
}
