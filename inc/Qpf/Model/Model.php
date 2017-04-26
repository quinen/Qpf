<?php

namespace Qpf\Model;

use Qpf\Model\Database\Database;

// Model
abstract class Model {
    
    public $db = "default";

    private $_Database = null;

    
    public function __construct(){
        $this->_Database = new Database($this->db);
    }

    public function __call($_fct,$_opt=array())
    {
        return call_user_func_array(array($this->_Database,$_fct),$_opt);
    }
    

};