<?php

namespace App\Controller;

use App\Model\Billet;

class IndexController extends Controller {
    
    public function init(){
        //$this->log($this->controller,array('trace'=>false));
    }    
    
    public function index(){
        
        
        $this->Billet = new Billet();
        $billets = $this->Billet->getBillets();
        
    }
    
    
}