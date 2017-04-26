<?php

namespace Qpf\View\Ext;

use Qpf\Core\Exception;
use Qpf\Core\Object;
use Qpf\Qpf;

class Html extends Object {
    
    protected   $_tags = array(
        'b','br'
        ,'code'
        ,'dl','dt','dd' // description list, term, description
        ,'p','pre'
    );
    
    private     $_isAutoClose = array("br","hr","input");
    
    
    /*
        prend le nom de la methode comme tag, sauf si la methode existe bel et bien
    */
    public function __call($_method,$_opt)
    {
//Qpf::pre($_method);Qpf::pre($this->_tags);Qpf::pre(array_keys($this->_tags));

        if(in_array($_method,$this->_tags))
        {
            array_unshift($_opt,$_method);
            return call_user_func_array(array($this,"tag"),$_opt);
        }
        else{
            throw new Exception("\$this->Html->".$_method."() unknown");
        }
    }
    
    private static function _tagOptToString($_opts)
    {
        $str = '';
        foreach($_opts as $k=>$v)
        {
            //if(!in_array($k,array('isAutoClose'))){
            if($v!==false)
            {
                $str .= ' '.$k.'="'.$v.'"';
            }
            //}
        }
        return $str;
    }     
    
    public function css($_css){
        if(is_array($_css)){
            return implode("",array_map(array($this,"css"),$_css));
        }
        return $this->tag('link',null,array(
            'rel'   => "stylesheet"
            ,'href' => "css/".$_css.".css"
        ));
    }
    
    public function dl($_data,$_maps = array(),$_opt = array()){
        $opt_defaults = array(

        );
        $opt = array_merge($opt_defaults,$_opt);

        if(empty($_maps)){

            $maps = array();

            foreach($_data as $k=>$v){
                $maps[] = array(
                    'label'     => $k
                    ,'value'    => $v
                );
            }
        } else {
            // traitement de $_maps
        }
        // map initialisé

        $html = "";

        foreach($maps as $k=>$map){
            $label = $map['label'];
            $value = $map['value'];

            $html .= $this->dt($label).$this->dd($value);
        }

        return $this->tag("dl",$html,$opt);
    }

    public function script($_script){
        return $this->tag('script',"",array(
            'src'=>"js/".$_script.".js"
        ));
    }
    
    public function tag($_tag,$_html=null,$_opt=array())
    {
        if(is_array($_html)){$_opt = $_html;}

        $opt_defaults = array(
            'isAutoClose'   => false
        );
        $opt_tag    = (isset($this->_tags[$_tag])?$this->_tags[$_tag]:array());
        $opt        = array_merge($opt_defaults,$opt_tag,$_opt);

        $optHtml = $this->_tagOptToString($opt);

        if(in_array($_tag,$this->_isAutoClose))
        {
            $html = '<'.$_tag.$optHtml.'/>';
        } else if(is_null($_html)){
            $html = '<'.$_tag.$optHtml.'>';
        } else {
            $html = '<'.$_tag.$optHtml.'>'.$_html.'</'.$_tag.'>';
        }

        return $html;
    }
    public function title($_title=null){
        if(!empty($_title)){
            return $this->tag('title',"".$_title);
        }
    }

   
}
