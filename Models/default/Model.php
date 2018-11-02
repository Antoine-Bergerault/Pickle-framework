<?php
namespace Pickle;

//this is a class for database request
require ROOT.'/Tools/db.php';
use Pickle\Tools\DB;
use Pickle\Tools\Config;

class Model{

    //define the tablename, this is required
    public $table = '';
    private $params = [
        "SELECT" => "*",
        "FROM" => false,
        "WHERE" => false,
        "ORDER BY" => false,
        "LIMIT" => false
    ];//params array for the queries
    private $args = [];

    public function __construct(){//constructor function
        $this->params["FROM"] = "`$this->table`";
        return $this;
    }

    public function all(){//return this, it's optionnal, can be used for more understandable code
        return $this;
    }

    public function select($arg){//select only arg. ex of $arg : ['name', 'email']
        if(is_string($arg)){
            $this->params['SELECT'] = "$arg";
        }else if(is_array($arg)){
            $c = "";
            $x = 0;
            foreach($arg as $a){//foreach, translate into SQL language
                if($x != 0){
                    $c .= ",";
                }
                $x++;
                $c .= $a;
            }
            $this->params["SELECT"] = $c;//change the select using the arguments
        }else{
            echo 'An error has occured';
            return false;
        }
        return $this;
    }

    public function from($table){//use it especially for inner join
        $this->params["FROM"] = $table;
    }

    //return the first occurence
    public function first(){
        return $this->find(1);
    }

    //return a number of occurence
    public function find($n, $f = 0){
        $this->params["LIMIT"] = "$f,$n";//adding the limit to the sql query
        return $this;
    }

    //a where condition is apply to the query
    public function where($arr){//ex of $arr : ['name' => 'LIKE A%','email' => '= test@test.com']
        $c = "";
        $x = 0;
        foreach($arr as $key => $v){//for each param, translate to SQL language
            if($x != 0){
                $c .= " AND";
            }
            $x++;
            $c .= " $key $v";
        }
        $this->params["WHERE"] = $c;//adding the conditions to the query
        return $this;
    }

    public function orderByDesc($param){//ORDER BY $param DESC
        $this->params["ORDER BY"] = $param." DESC";
        return $this;
    }

    public function orderByAsc($param){//ORDER BY $param ASC
        $this->params["ORDER BY"] = $param." ASC";
        return $this;
    }
    
    public function args($arr){
        $this->args = $arr;
        return $this;
    }

    public function run(){//do forget this to run the query
        $sql = $this->sql();
        return $this->query($sql,$this->args);
    }

    public function reset(){
        $this->params = [
            "SELECT" => "*",
            "FROM" => false,
            "WHERE" => false,
            "ORDER BY" => false,
            "LIMIT" => false
        ];
        return $this;
    }

    public function sql(){//create the sql request
        $str = "";
        foreach($this->params as $key => $val){
            if($val != false){
                $str .= "$key $val ";
            }
        }
        return $str;
    }

    public function query($sql,$data = array()){//execute the request
        $db = new DB(Config::$host, Config::$username, Config::$password, Config::$database);
        return $db->query($sql,$data);
    }

    public function create($arg, $table = false, $pass = true){//create a new element using the $arg. ex : ["name" => "Test", "email" => "test@test.com"]
        $keys = array_keys($arg);
        if($table == false){
            $table = $this->table;
        }
        //$sql = "INSERT INTO `users` (`id`, `name`, `email`, `pass`) VALUES (NULL, '', '', '')";
        $sql = "INSERT INTO $table (";
        $n = 0;
        foreach($keys as $key){
            if($n>0){
                $sql .= ", ";
            }
            $sql .= "`$key`";
            $n++;
        }
        $sql .= ") VALUES (";
        $arr = [];
        $n = 0;
        foreach($arg as $k => $v){
            if($k == 'pass' && $pass == true){
                $v = password_hash($v, PASSWORD_BCRYPT);
            }
            $arr[] = $v;
            $sql .= "?";
            if($n != sizeof($arg) - 1){
                $sql .= ", ";
            }
            $n++;
        }

        $sql .= ")";
        $this->query($sql,$arr);
        return $sql;

    }

    public function lastinsertid($c = 'id'){//return the last inserted id
        $obj = $this->query("SELECT $c FROM $this->table ORDER BY $c DESC LIMIT 1")[0];
        $arr = (array) $obj;
        return $arr["$c"];
    }

    public function count(){
        $this->params['SELECT'] = 'COUNT(*)';
        return $this;
    }

    public function fromcount($arr){
        if(!empty($arr)){
            $arr = (Array) $arr[0];
            $arr = (int) reset($arr);
        }else{
            $arr = 0;
        }
        return $arr;
    }

}


?>