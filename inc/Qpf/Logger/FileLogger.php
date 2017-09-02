<?php

namespace Qpf\Logger;

use \Psr\Log\AbstractLogger;

use \Qpf\Core\File;
use \Qpf\Core\Text;


class FileLogger extends AbstractLogger
{
    private $_file  = array();
    
    private $_path  = QPF_DIR_LOG;
    
    public function log($_lvl,$_msg,array $_ctx = array())
    {
        // file instanciation
        if(!(isset($this->_file[$_lvl]) && $this->_file[$_lvl])){
            $this->_file[$_lvl] = new File($this->_getFilename($_lvl),"a");
        }
        
        // context
        $ctx_defaults = array(
            'timestamp' => date("Y-m-d H:i:s")
            ,'type'     => false
            ,'trace'    => true
        );
        $ctx = array_merge($ctx_defaults,$_ctx);
        
        $ctxFormatted = array();
        // format
        foreach($ctx as $k=>$v){
            $methodName = "_format".ucfirst($k);
            $value = $v;
            
            if($v && method_exists($this,$methodName)){
                $value = call_user_func(array($this,$methodName),$v);
            }

            if($value){
                $ctxFormatted[] = "{".$k."}";
                $ctx[$k]        = $value;
            } else {
                unset($ctx[$k]);
            }
        }
        
        $ctxFormatted[] = $_msg;
        
        $msg = implode(" | ",$ctxFormatted).PHP_EOL;
        
        foreach($ctx as $k=>$v){
            $msg = str_replace("{".$k."}",$v,$msg);
        }

        $this->_file[$_lvl]->write($msg);
    }
    
    private function _getFilename($_lvl)
    {
        return $this->_path.DS.$_lvl.".log";
    }
    
    private function _formatTrace($_trace){
        // file,line,function,class,object,type,args        
        if($_trace === true){$_trace = "4-6";}

        $str    = "";
        $dbbt   = debug_backtrace();
        $nbDbbt = count($dbbt);
        
        $traceArray = explode("-",$_trace);
        if(count($traceArray)==2){
            $start  = min($traceArray[0],$nbDbbt);
            $end    = min($traceArray[1],$nbDbbt);
        } else {
            $start  = min($traceArray[0],$nbDbbt);
            $end    = $nbDbbt;
        }
        
//echo "<pre>".var_export(compact('start','end'),true)."</pre>";
        
        for($i=$start;$i<$end;$i++){
            $line = $dbbt[$i];
            $str .= "".(new Text("[".($i)."]"))->shellColor('yellow');
            
            // file:line
            if(isset($line['line'])){
                $str .= str_replace(QPF_DIR.DS,"",$line['file']).":".$line['line']." ";
            }
            
            // class type function args
            if(isset($line['class'])){
                //$nbArgs = count($line['args']); // ".($nbArgs?$nbArgs:"")."
                $str    .= $line['class'].$line['type'].$line['function']."()";
            }
            
            // object
            //$str .= "\t|||".json_encode($line);
        }
        
        // if true > no limit
        return $str;
    }
    
    private function _formatType($_type){
        return substr(str_pad($_type,16),0,16);
    }
    
}