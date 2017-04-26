<?php

namespace Qpf\Http;

use Qpf\Core\Exception;
use Qpf\Core\Object;
use Qpf\View\View;

use App\Controller\Controller;

class Dispatcher extends Object {
    
    public $request     = null;
    public $response    = null;

    public function __construct($_request,$_response){
        $this->request  = $_request;
        $this->response = $_response;
    }
    
    private function _getController($_namespace="App"){
        $controllerName     = ucfirst($this->request->controller);
        $controllerClass    = $_namespace."\Controller\\".$controllerName."Controller";
        
        if(!class_exists($controllerClass)){
            throw new Exception("Controller ".$controllerClass." non defini");
        }
        
        return new $controllerClass();        
    }
        
    public function dispatch(){
        try {
            $Controller = $this->_getController();
            $action     = $this->request->action;
            $params     = $this->request->params;
            
            // set request
            $Controller->request = $this->request;
            
            // run action
            $Controller->runAction($action,$params);
        } catch (Exception $e) {
            $this->dispatchError($e);
        }        
    }
    
    public function dispatchError(Exception $exception) {
        
        try {
            $controller = new Controller();
$this->log($controller->theme);
            $vue        = new View('error',$controller);            
            $vue->generate(array('e' => $exception));
        } catch (Exception $e) {
            echo $e->getMessage();
        }            
    }    
}
