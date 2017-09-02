<?php

namespace Qpf\Core;

class Text extends Object
{
    private $_string    = "";
    private $_length    = 0;

    public function __construct($_string="")
    {
        $this->_string  = $_string;
        $this->_length  = strlen($this->_string);
    }
    
    public function shellColor($_colors)
    {
        $formats = array(
            'default'       => 0
            ,'bold'         => 1
            ,'underline'    => 4
            ,'blink'        => 5
            ,'highlight'    => 7
           
            ,'black'        => 30
            ,'red'          => 31
            ,'green'        => 32
            ,'yellow'       => 33
            ,'blue'         => 34
            ,'magenta'      => 35
            ,'cyan'         => 36
            ,'grey'         => 37

            ,'bg_black'     => 40
            ,'bg_red'       => 41
            ,'bg_green'     => 42
            ,'bg_yellow'    => 43
            ,'bg_blue'      => 44
            ,'bg_magenta'   => 45
            ,'bg_cyan'      => 46
            ,'bg_grey'      => 47            
        );
        
        $colorsArray = explode(" ",$_colors);
        
        $shells = array();
        foreach($colorsArray as $color){
            if(isset($formats[$color])){
                $shells[] = $formats[$color];
            }
        }
        
        $this->_string = "\033[".implode(";",$shells)."m".$this->_string."\033[0m";
        
        return $this->_string;
    }
}