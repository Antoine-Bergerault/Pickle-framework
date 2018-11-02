<?php
namespace Pickle;
require_once 'default/Model.php';

class UserModel extends Model{

    //defined the table name for this model
    public $table = 'users';

    public function users_info($arg = array(), $restrict = false, $limit = null, $from = 0, $add = ''){//get the user info
        //$sql = "SELECT * FROM `$this->table` ";
        $sql = "SELECT $this->table.*, GROUP_CONCAT(groups.group_id) as group_id_array, GROUP_CONCAT(groups.group_name) as group_name FROM `$this->table` 
        INNER JOIN users_group ON users.id = users_group.id_users 
        INNER JOIN groups ON users_group.id_group = groups.group_id ";
        $narg = $arg;
        $vals = [];
        unset($narg['pass']);
        foreach($narg as $k => $v){
            $vals[] = $v;
        }
        if($limit == null && $restrict == true){
            $limit = true;
        }
        $sql = $this->condition_query($sql, $narg);
        $sql .= "GROUP BY users.id $add";
        if($limit){$sql .= " LIMIT $from, 1";};
        $usr = $this->query($sql,$vals);
        if(empty($usr)){
            $sql = "SELECT * FROM `$this->table` ";
            $sql = $this->condition_query($sql, $narg);
            $usr = $this->query($sql,$vals);
        }
        if(!isset($usr[0])){
            return false;
        }
        if($restrict){
            $usr = (object) $usr[0];
            if(isset($usr->group_id_array)){
                $usr->group_id_array = explode(',', $usr->group_id_array);
                $usr->group_name = explode(',', $usr->group_name);
            }else{
                $usr->group_id_array = [];
                $usr->group_name = [];
            }
            if(!empty($usr->group_id_array)){
                $usr->actions = [];
                foreach($usr->group_id_array as $group_id){
                    $Actions = new ActionModel();
                    $usr->actions = array_merge($usr->actions, $Actions->all_actions($group_id));
                }
            }else{
                $usr->actions = [];
            }
        }else{
            foreach($usr as $c){
                if(isset($c->group_id_array)){
                    $c->group_id_array = explode(',', $c->group_id_array);
                    $c->group_name = explode(',', $c->group_name);
                }else{
                    $c->group_id_array = [];
                    $c->group_name = [];
                }
                if(!empty($c->group_id_array)){
                    $c->actions = [];
                    foreach($c->group_id_array as $group_id){
                        $Actions = new ActionModel();
                        $c->actions = array_merge($c->actions, $Actions->all_actions($group_id));
                    }
                }else{
                    $c->actions = [];
                }
            }
        }
        return $usr;
    }

    public function user_info($arg){
        return $this->users_info($arg, true);
    }

    public function get_user($arg){//get the user info only if the pass are correct (used to connect users)
        $usr = $this->user_info($arg);
        if($usr != false){
            if(isset($usr->pass) && password_verify($arg['pass'], $usr->pass)){
                return $usr;
            }
        }
        return false;
    }

    public function condition_query($sql, $arg){
        if(sizeof($arg) > 0){
            $sql .= "WHERE ";
            $n = 0;
            foreach($arg as $k => $v){
                if($k != 'pass'){
                    $sql .= "$k = ? ";
                    
                    if($n != sizeof($arg) - 1){
                        $sql .= "AND ";
                    }
                }
                $n++;
            }
        }
        return $sql;
    }

    public function updateRole($role, $user_id,$ifNotCreate = true){
        $sql = "DELETE FROM users_group WHERE id_users = $user_id";
        $this->query($sql);
        if($role != 'user'){
            $sql = "SELECT group_id FROM groups WHERE group_name = '$role' LIMIT 1";
            $group_id = $this->query($sql);
            if(!empty($group_id)){
                $group_id = $group_id[0];
                $group_id = $group_id->group_id;
                $sql = "INSERT INTO users_group (users_group.id_users,users_group.id_group) VALUES ($user_id,$group_id)";
                $this->query($sql);
            }elseif($ifNotCreate == true){
                $this->create([
                    "group_name" => "$role"
                ],'groups');
                $sql = "SELECT group_id FROM groups WHERE group_name = '$role' LIMIT 1";
                $group_id = $this->query($sql);
                $group_id = $group_id[0];
                $group_id = $group_id->group_id;
                $sql = "INSERT INTO users_group (users_group.id_users,users_group.id_group) VALUES ($user_id,$group_id)";
                $this->query($sql);
            }
        }
        return true;
    }

}