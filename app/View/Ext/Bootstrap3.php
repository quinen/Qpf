<?php

namespace App\View\Ext;

use Qpf\Core\Data;
use Qpf\View\Ext\Html;

class Bootstrap3 extends Html {
    
    public function dl($_data,$_maps = array(),$_opt = array()){
        $opt_defaults = array(
            'isHorizontal'  => true
        );
        $opt = array_merge($opt_defaults,$_opt);
$this->log($opt);

        if($opt['isHorizontal']){
            $opt = Data::concat($opt,'class'," dl-horizontal");
        }
        unset($opt['isHorizontal']);
$this->log($opt);

        return parent::dl($_data,$_maps,$opt);
    }


/*
echo $this->Bs3->panel(array(
        'heading'   => "Une erreur est survenue : "
        ,'body' => $this->Bs3->dl($data)
        ,'content'  => "content" // table or list 
        ,'footer'   => "footer"
        ,'context'  => "danger"
    )
);
*/
    public function panel($_body_or_content,$_opt=array()){
        if(is_array($_body_or_content)){
            $_opt = $_body_or_content;
        } else {

        }
    }
   
}
