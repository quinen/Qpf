<?php

namespace Qpf\Core;

use Qpf\Core\Object;

class File extends Object 
{
    private $_resource      = null;
    
    private $_mode          = null;
    
    private $_dirname       = null;
    
    private $_basename      = null;
    
    public function __construct($_fullname,$_mode = "r")
    {
        $this->_dirname     = dirname($_fullname);
        $this->_basename    = basename($_fullname);
        $this->_mode        = $_mode;

        $this->_open();

    }
    
    /*
        open the file and set the ressource
    */
    private function _open()
    {
        if(!is_dir($this->_dirname)){
            // silent warning, in general, permission involved
            if(!@mkdir($this->_dirname,0766,true)){
                throw new \Exception("Cannot create folder '".$this->_dirname."' : check your permissions");
            }
        }
        
        $resource = @fopen($this->getFullname(),$this->getMode());
        
        if(!$resource){
            throw new \Exception("Cannot open file '".$this->getFullname()."' in mode '".$this->getMode()."' : check your permissions");
        }
        
        $this->_resource = $resource;
    }    

    public function close()
    {
        $return             = fclose($this->_resource);
        $this->_resource    = null;
        return $return;
    }

    public function delete()
    {        
        $this->close();
        $result = unlink($this->getFullname);
        return $result;
    }

    public function getFullname()
    {
        return $this->_dirname.DIRECTORY_SEPARATOR.$this->_basename;
    }
    
    public function getMode()
    {
        return $this->_mode;
    }

    public function getResource(){
        if(!$this->_resource){
            $this->_open();
        }
        return $this->_resource;
    }

    public function isExists()
    {
        return file_exists($this->getFullname());
    }
    
    public function read()
    {
        $result = fread($this->getResource(),filesize($this->getFullname()));
        $this->close();
        return $result;
    }
    
    public function write($_data)
    {
        $result = fwrite($this->getResource(),$_data);
        $this->close();
        return $result;
    }
}
