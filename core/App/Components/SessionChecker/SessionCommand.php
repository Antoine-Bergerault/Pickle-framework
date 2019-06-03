<?php
namespace Pickle\Engine\SessionChecker;

class SessionCommand{
    
    public $action = false;
    public $update_method = false;
    public $update_value = null;
    public $update_sql = null;
    public $update_sql_data = null;
    public $first_sql_object = false;
    public $update_data = null;
    public $filter = null;
    public $filter_values = null;

    public function __construct($action = false){
        $this->action = $action;
    }

    public function rebuild($data){
        foreach($data as $k => $v){
            $this->$k = $v;
        }
        return $this;
    }

    public function reduce(){
        return [
            'action' => $this->action,
            'update_method' => $this->update_method,
            'update_value' => $this->update_value,
            'update_sql' => $this->update_sql,
            'update_sql_data' => $this->update_sql_data,
            'update_data' => $this->update_data,
            'first_sql_object' => $this->first_sql_object,
            'filter' => $this->filter,
            'filter_values' => $this->filter_values
        ];
    }

    public function setUpdateMethod($method, $data = false, $extra = null, $first_sql_object = false){
        $this->update_method = $method;
        if($method == 'value'){
            $this->update_value = $data;
        }else if($method == 'sql'){
            $this->update_sql = $data;
            $this->update_sql_data = $extra;
            $this->first_sql_object = $first_sql_object;
        }else if($method == 'user_info'){
            $this->update_data = $data;
        }
        return $this;
    }

    public function getNewValue(){
        if($this->action == 'delete'){
            return null;
        }else if($this->action == 'clear'){
            return '';
        }else if($this->action == 'update'){
            $method = $this->update_method;
            $value = null;
            if($method == 'value'){
                $value = $this->update_value;
            }else if($method == 'sql'){
                $sql = $this->update_sql;
                $data = $this->update_sql_data;
                require_once ROOT.'/src/Models/default/Model.php';
                $Model = new \Pickle\Model();
                $value = $Model->query($sql, $data);
                if($this->first_sql_object){
                    $value = $value[0];
                }
            }else if($method == 'user_info'){
                return \Pickle\Engine\App::getuserbydata($this->update_data);
            }
            return $value;
        }
    }

    public function filter($condition, $data){
        if(in_array($condition, ['user_id'])){
            $this->filter = $condition;
            $this->filter_values = $data;
            return $this;
        }
    }

    public function isActive(){
        if(in_array($this->filter, ['user_id'])){
            if(!is_array($this->filter_values)){
                $this->filter_values = [$this->filter_values];
            }
            if($this->filter == 'user_id'){
                $v = false;
                foreach($this->filter_values as $value){
                    if($value == '*'){
                        $v = true;
                    }else if($value[0] == '!'){
                        $id = (int) substr($value, 1);
                        $v = \Pickle\Engine\App::getid() != $id;
                    }else if($v == false){
                        $v = \Pickle\Engine\App::getid() == $value;
                    }
                }
                return $v;
            }
            return true;
        }
        return true;
    }

}