<?php
namespace Pickle;
//import the model reference
require_once 'default/Model.php';

//this is the test Model

class ActionModel extends Model{

    //defined the table name for this model
    public $table = 'privilege';

    public function all_actions($group_id){
        $sql = "SELECT $this->table.action FROM $this->table
        INNER JOIN group_privilege ON $group_id = group_privilege.id_group
        WHERE $this->table.id = group_privilege.id_privilege";
        return $this->change($this->query($sql));
    }

    public function change($arr){
        $res = [];
        foreach($arr as $action){
            $narr = get_object_vars($action);
            $action = $narr['action'];
            $res[] = $action;
        }
        return $res;
        
    }

}



?>