<?php
namespace Qpf\Core;

require QPF_DIR_INC."/Psr/Autoloader.php";
use Psr\Autoloader as PsrAutoloader;

/*
$al->addNamespace("Psr","inc/Psr");
$al->addNamespace("Qpf","inc/Qpf");
$al->register();
*/

class Autoloader extends PsrAutoloader 
{
    private $_options = array(
        'namespaces'    => array(
            "Psr","Qpf"
        )
        ,'autoRegister' => true
    );
    
    public function __construct($_opt=array()){
        $this->_options = array_merge($this->_options,$_opt);
        
        // namespaces
        foreach($this->_options['namespaces'] as $namespace){
            if(is_string($namespace)){
                $namespace = array($namespace,QPF_DIR_INC.DIRECTORY_SEPARATOR.$namespace);
            }
            call_user_func_array(array($this,"addNamespace"),$namespace);
        }
        
        // auto register
        if($this->_options['autoRegister']){
            $this->register();
        }
    }
    
    public function addNamespace($prefix, $base_dir, $prepend = false)
    {
        parent::addNamespace($prefix,$base_dir,$prepend);
        return $this;
    }        
}

