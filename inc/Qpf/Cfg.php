<?php

namespace Qpf;

use Qpf\Core\Data;

class Cfg {
    
    const DEBUG_DEFAULT   = 1;
    const DEBUG_SQL       = 2;
    const DEBUG_ALL       = 3;

    const LOG_DEFAULT   = 1;
    const LOG_SQL       = 2;
    const LOG_ALL       = 3;    
    
    private static $_cfg = array();
    
    public static function get($_key){
        return Data::get(self::$_cfg,$_key);
    }
    
    public static function set($_key,$_value){
        return self::$_cfg = Data::set(self::$_cfg,$_key,$_value);
    }  

    /*
        false = never show
        true = always
        other = level
    */
    public static function debug($_level = DEBUG_DEFAULT)
    {
        //$debug = K::get('Lettre.debug');return $debug & $_level;
        return Cfg::get('debug') & $_level;
    }    
    
}
