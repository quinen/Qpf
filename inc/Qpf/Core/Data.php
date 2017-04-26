<?php

namespace Qpf\Core;

class Data {
    
    public static function getOld($_data,$_path = null,$_default = null,$_opt = array()){
        // si path a vide alors on renvoi la donnée
        if(empty($_path)){
            return $_data;
        }        
        
        // separateur
        $opt_defaults = array(
            'separator' => "."
        );
        $opt = array_merge($opt_defaults,$_opt);
        
        // si on traite bien un array
        if(is_array($_data)){
            $path   = explode($opt['separator'],$_path);
            $key    = array_shift($path);
            if(isset($_data[$key])){
                return Data::get($_data[$key],$path,$_default,$opt);
            }
        }
        
        return $_default;
    }

    public static function setOld($_data,$_path,$_value,$_opt = array()){

        if(empty($_path)){
            return $_value;
        }
        
        // separateur
        $opt_defaults = array(
            'separator' => "."
        );
        $opt = array_merge($opt_defaults,$_opt);
        
        if(is_string($_path)){
            $path   = explode($opt['separator'],$_path);
        } else {
            $path   = $_path;
        }

        $key            = array_shift($path);

        if(isset($_data[$key])){
            $_data[$key]    = Data::set($_data[$key],$path,$_value,$opt);
        } else {
            $_data[$key]    = Data::set(array()     ,$path,$_value,$opt);
        }

        return $_data;
    }
    
    public static function get($_array, $_path = null, $_opt = array())
    {
        if(is_null($_path)){
            return $_array;
        }
        
        if(is_scalar($_opt) || is_null($_opt))
        {
            $_opt = array('default'=>$_opt);
        }

        $opt_defaults = array(
            'default'       => null
            ,'separator'    => "."
        );
        $opt = array_merge($opt_defaults,$_opt);

        $value  = $opt['default'];
        $path   = explode($opt['separator'],$_path);

        $a = $_array;

        foreach($path as $key)
        {
            if(is_array($a) && isset($a[$key]))
            {
                $a = $a[$key];
            }
            else
            {
                return $opt['default'];
            }
        }

        return $a;
    }

    public static function set($_array,$_path = null,$_value = null)
    {
        if(is_scalar($_path) || is_null($_path))
        {
            $path = explode('.',$_path);
        }
        else{
            $path = $_path;
        }
        $a =& $_array;
        foreach($path as $k=>$v)
        {
            if(!isset($a[$v])){
                $a[$v] = array();
            }
            $a =& $a[$v];
        }
        $a = $_value;
        return $_array;
    }

    public static function concat($_array,$_path = null,$_value = null){
        $data = Data::get($_array,$_path);
        if(is_null($data)){
            $_array = Data::set($_array,$_path,trim($_value));
        }else{
            $_array = Data::set($_array,$_path,$data.$_value);
        }
        return $_array;
    }
    
    /*
        applanit une structure de données au format liste pour affichage
    */
    function structureToList($_array,$_opt=array())
    {
        $opt_defaults = array(
            'separator' => "."
        );
        $opt = array_merge($opt_defaults,$_opt);

        if(!isset($_array[0])){
            $_array = array($_array);
        }

        $return = array();

        $isAnyArray = false;

        foreach($_array as $v)
        {
            $scalars = array();
            foreach($v as $l=>$w)
            {
                if(is_scalar($w))
                {
                    $scalars[$l] = $w;
                }
            }

            $isArray = false;

            foreach($v as $l=>$w)
            {
                if(is_array($w))
                {
                    $isAnyArray = $isArray = true;
                    foreach($w as $m=>$x)
                    {
                        $xPrefixed = array();
                        foreach($x as $n=>$y)
                        {
                            $xPrefixed[$l.$opt['separator'].$n] = $y;
                        }
                        $return[] = array_merge($scalars,$xPrefixed);
                    }
                }
            }

            if(!$isArray)
            {
                $return[] = $scalars;
            }
        }

        if($isAnyArray)
        {
            return self::structureToList($return,$opt);
        }
        else
        {
            return $_array;
        }
    }
    
    /*
        transpose
        array(
            array(field1,field2)
            ,array(value1,value2)
            ,array(value3,value4)
        )
        to
        array(
            array(field1=>$value1,field2=>value2)
            array(field1=>$value3,field2=>value4)
        )
    */
    public static function transpose($_array)
    {
        $a      = array();
        $fields = array_shift($_array);
        $empty  = array_fill(0,count($fields),null);

        foreach($_array as $line)
        {
            $a[] = array_combine($fields,$line+$empty);
        }
        return $a;
    }    
}
