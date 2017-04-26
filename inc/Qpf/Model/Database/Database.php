<?php

namespace Qpf\Model\Database;

use Qpf\Cfg;
use Qpf\Qpf;
use Qpf\Core\Exception;
use Qpf\Core\Object;

use PDO;
use PDOException;

// Data Base
class Database extends Object {
    private $_configKey;
    private $_config;
    private $_connection    = null;

    public function __construct($_configKey="default")
    {
        $this->_configKey   = $_configKey;
        
        $_config = Cfg::get("DB.".$this->_configKey);
        
        if(is_null($_config)){
            throw new Exception("no DB.".$this->_configKey." set in /app/Cfg/[factory|local].php");
        }
        
        $this->_config      = $this->populateDsn($_config);
    }
    
    public function query($_query,$_params=array(),$_opt=array())
    {
        $opt_defaults = array(
            'fetchStyle' => PDO::FETCH_ASSOC
        );
        $opt = array_merge($opt_defaults,$_opt);

        if(Cfg::debug(Cfg::LOG_SQL)){
            F::log(preg_replace_callback("/(select|from|where|order\sby|create\stable)/i",function($m){
                return "\n\t".strtoupper($m[0])."\t";
            },$_query));
            $this->log($_query,array('showBT'=>false,'showType'=>true,'type'=>"SQL:query"));
        }
        
        $sql = false;
        
        try{
            if ($_params) {
                $sql = $this->db()->prepare($_query); // requête préparée
                $sql->execute($_params);
            }
            else {
                $sql = $this->db()->query($_query);   // exécution directe
            }
        }catch(PDOException $e){
            $this->log($e->getMessage(),array('color'=>"red"));
        }
        
        $return = $sql;
        
        if($sql){
            $return = $sql->fetchAll($opt['fetchStyle']);
            if(K::debug(K::LOG_SQL)){
                $this->log(json_encode($return),array('showBT'=>false,'showType'=>true,'type'=>"SQL:results"));
            }                    
        }

        return $return;
    }

    public function populateDsn($_keys=array())
    {
        // mysql:host=localhost;dbname=testdb
        
        $keys_defaults = array(
            'type'      => "sqlite"
            ,'file'     => true
            ,'host'     => false
            ,'charset'  => "utf8"
            ,'dbname'   => false
            ,'login'    => null
            ,'password' => null
        );
        $keys = array_merge($keys_defaults,$_keys);
        
        $dsn = $keys['type'].":";
        
        // host/file
        if($keys['host']){
            $dsn    .= "host=".$keys['host'].";";
            
            // dbname
            if($keys['dbname']){
                $dsn    .= "dbname=".$keys['dbname'].";";
            }
            
            // charset
            if($keys['charset']){
                $dsn    .= "charset=".$keys['charset'].";";
            }
        } elseif($keys['file']){
            if($keys['file']===true){
                $keys['file'] = QPF_DIR_TMP.DS.$this->_configKey.".db";
            }
            $dsn    .= $keys['file'];
        }

        $keys['dsn'] = $dsn;
        return $keys;
    }
    
    public function db() {
        
        if (!isset($this->_connection)) {
            $this->_connection = new PDO(
                $this->_config['dsn']
                , $this->_config['login']
                , base64_decode($this->_config['password'])
                ,array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                )
            );
        }
        return $this->_connection;
    }
    
    // renvoi la liste des tables
    public function tables()
    {
F::log($this->_config['type']);
        if($this->_config['type']=="sqlite")
        {
            $tables = $this->query("SELECT name FROM sqlite_master where type = 'table' order by name;");
        }       
        else
        {
            $tables = $this->query('SHOW TABLES');
        }
$tables = array_map("reset",$tables);
F::log($tables);
        return $tables;
    }
    
    public function tableCreate($_table,$_columns)
    {
        $str     = "CREATE TABLE IF NOT EXISTS ".$_table."";
        $columns = array();
        foreach(A::transpose($_columns) as $column)
        {
            $columns[] = $this->columnCreate($column);
        }
        $strColumns =  implode(", ",$columns);

        $str .= " (".$strColumns.PHP_EOL.");";

        return $this->query($str);
    }

    public function tableDrop($_table){
        $str = "DROP TABLE IF EXISTS ".$_table.";";
        return $this->query($str);
    }
    
    public function columnType($_type,$_length)
    {
        $types = array(
            'sqlite'    => array(
                "NULL"      => array("null")                
                ,"INTEGER"  => array("int")
                ,"REAL"     => array("")
                ,"TEXT"     => array("varchar","char")
                ,"BLOB"     => array("blob")
            )
        );
        $lengths = array(
            'sqlite'    => array(
                "NULL"      => false
                ,"INTEGER"  => false
                ,"REAL"     => false
                ,"TEXT"     => false
                ,"BLOB"     => false
            )
        );

        foreach($types[$this->_config['type']] as $k=>$v)
        {
            if(in_array($_type,$v))
            {
                $type = $k;
                if($lengths[$this->_config['type']][$k]){
                    $type .= "(".$_length.")";
                }
                
                return $type;
            }
        }
        return $_type;
    }

    public function columnCreate($_column)
    {
        $column_defaults = array(
            'name'          => null
            ,'type'         => null
            ,'length'       => null
            ,'null'         => null
            ,'constraint'   => null
        );
        $column = array_merge($column_defaults,$_column);
        
        $str = PHP_EOL;
        // name
        $str .= $column['name'];
        // type
        $str .= " ".$this->columnType($column['type'],$column['length']);
        // null
        if(!is_null($column['null'])){
            if($column['null']){
                $str .= " NULL";
            }else{
                $str .= " NOT NULL";
            }
        }
        // constraint
        if(!is_null($column['constraint'])){
            $str .= " ".$column['constraint'];
        }
        return $str;
    }
    
    public function dbType()
    {
        return A::get($this->_db,$this->db.".type");
    }
    
    public function init(){

        $db = K::get('DB_INIT.'.$this->_configKey);

        if(is_array($db['create']))
        {
            // create
            foreach($db['create'] as $tableArray)
            {
                call_user_func_array(array($this,"tableCreate"),$tableArray);
            }
        }
        else
        {
            $this->log('absence de create pour '.$this->_configKey);
        }
    }
};
