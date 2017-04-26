<?php

namespace Qpf\View;

use Qpf\Core\Data;
use Qpf\Core\Exception;
use Qpf\Core\Object;
use Qpf\Controller\Controller;
use Qpf\Qpf;


class View extends Object {
    
    private $_data      = array();
    private $_dataView  = array();

    private $_folder    = "";
    private $_file      = null;
    private $_extension = "php";
    
    private $_ext       = array();

    public $view        = null;    
    
    // controller vars
    public $controller  = null;
    public $action      = null;
    public $layout      = 'default';
    public $theme       = 'default';
        
    public function __construct($_view,$_opt = array()) {

        if($_opt instanceof Controller){
            $_opt = array('controller' => $_opt);
        }
        
        $opt_defaults = array(
            'controller'    => null
            ,'viewExt'      => array()
        );
        $opt = array_merge($opt_defaults,$_opt);
        
        // controller
        if($opt['controller'] instanceof Controller)
        {
            $this->log($opt['controller']);
            // vars
            $this->controller   = $opt['controller']->controller;
            $this->action       = $opt['controller']->action;
            $this->layout       = $opt['controller']->layout;
            $this->theme        = $opt['controller']->theme;
            
            // extensions
            $opt['viewExt'] = array_merge($opt['viewExt'],$opt['controller']->viewExt);
        }
        
        // viewExt
        $this->_initAllViewExt($opt['viewExt']);
        
        // view
        $this->view = $_view;
    }
    
    public function __get($_name=null)
    {
        $value = Data::get($this->_ext,$_name);
        if(is_null($value)){
            return Data::get($this->_data,$_name);
        } else {
            return $value;
        }
    } 

    public function __set($_name=null,$_value)
    {
        $this->_data = Data::set($this->_data,$_name,$_value);
    }    
    
    private function _generateFile($_file,$_data=array()) {
        if (file_exists($_file)) {
            extract($_data);
            ob_start();
            require $_file;
            return ob_get_clean();
        }
        else {
            throw new Exception("Fichier '".$_file."' introuvable");
        }
    }

    private function _getFullFilename($_opt=array()){
        $opt_defaults = array(
            'theme'         => $this->theme
            ,'controller'   => $this->controller
            ,'view'         => $this->view
            ,'extension'    => $this->_extension
        );
        $opt = array_merge($opt_defaults,$_opt);

        $folders = array(
            QPF_DIR_APP.DS."View".DS."Theme".DS.$opt['theme'].DS
            ,QPF_DIR_APP.DS."View".DS
            
            
                //,QPF_DIR_INC.DS."Qpf".DS."View".DS."Theme".DS.$opt['theme'].DS

            ,QPF_DIR_INC.DS."Qpf".DS."View".DS."Theme".DS."default".DS
        );

        $controllerPath = ($opt['controller']?$opt['controller'].DS:"");

        $postPath = $controllerPath.$opt['view'].".".$opt['extension'];

        foreach($folders as $folder){
$this->log($folder.$postPath);
            if(file_exists($folder.$postPath)){
                return $folder.$postPath;
            }
        }

        throw new Exception("Fichier '".$postPath."' introuvable\n"
            ."Veuillez en creer un dans un des ces dossiers :\n".implode("\n",array_unique($folders))
        );


    }

    private function _initAllViewExt($_viewExts){
//Qpf::pre($_viewExts);
        foreach($_viewExts as $k=>$viewExt){
            $extOptions = array();
            if(is_int($k)){
                $extName = $viewExt;
            } else {
                $extName    = $k;
                $extOptions = $viewExt;
            }
            $this->_initViewExt($extName,$extOptions);
        }
    }
    
    private function _initViewExt($_name,$_opt=array()){

        if(is_string($_opt)){
            $_opt = array('class'=> $_opt);
        }

        $opt_defaults = array(
            'class' => $_name
        );
        $opt = array_merge($opt_defaults,$_opt);

        
        $extClasses = array(
            "App\View\Ext\\".$opt['class']
            ,"Qpf\View\Ext\\".$opt['class']
        );

        foreach($extClasses as $extClass){
            if(class_exists($extClass)){
                $this->_ext[$_name] = new $extClass;
                return;
            }
        }

        throw new Exception("Extension de vue '".$opt['class']."' aliasé '".$_name."' introuvable\n"
            ."Veuillez en creer une dans un des ces dossiers :\n".implode("\n",array_unique($extClasses))
        );
    }
    
    public function element($_element,$_data=array()){
        $data = array_merge($this->_dataView,$_data);
        $controller = (is_null($this->controller)?$this->controller.DS:"");
        return $this->_generateFile(
            $this->_folder.DS.$controller.$_element.".php"
            ,$data
        );
    }    
    
    public function generate($_data) {
        $data_defaults = array(
            //'root'      => K::get('Lettre.root').DS
            //,
            'title'    => null
            ,'content'  => null
        );
        
        // on remplace les données par defaut par la personnalisation de la page ; titre contenu
        $this->_data    = array_merge($data_defaults,array_intersect_key($_data,$data_defaults));
        // on ne recupere que les champs hors standard
        $this->_dataView = array_diff_key($_data,$data_defaults);
//$this->log($this->_file);

        $this->_file = $this->_getFullFilename();
        // generate view
        $this->content  = $this->_generateFile($this->_file,$this->_dataView);
        
        if($this->layout)
        {   
            $layoutPath = $this->_getFullFilename(array(
                'controller'    => null
                ,'view'         => "Layouts".DS.$this->layout
            ));

            $view = $this->_generateFile($layoutPath);
        }
        else
        {
            $view = $this->content;
        }
        
        echo $view;
    }    
    
}

