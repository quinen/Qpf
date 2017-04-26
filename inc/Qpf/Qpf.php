<?php

namespace Qpf;

use Qpf\Core\Data;
use Qpf\Core\Object;

class Qpf
{

	public static function __callStatic($_name,$_opt=array()){
		return call_user_func_array(array($this,$name), $_opt);
	}

    // ecrit dans les logs
    public function log($_msg,$_opt=array()){
        return (new Object())->log($_msg,$_opt);
    }
    
    // ecrit en sortie standard
    public function pre($_msg){
        $db = debug_backtrace(null,1);
        echo "<pre>y<b>".Data::get($db,'0.file').":".Data::get($db,'0.line')."</b>".PHP_EOL.var_export($_msg,true)."</pre>";
    }
}