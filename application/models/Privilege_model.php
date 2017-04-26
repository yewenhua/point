<?php
class Privilege_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * 插入单条记录...
	 */
	public function insert($data) {
		$param = array(
			"page_name"=>$data['page_name'],
			"page_state"=>$data['page_state'],
		    "page_url" => $data['page_url'],
		    "page_desc"=>$data['page_desc'],
		);
			
		$this->db->insert('privilege', $param);
		if($this->db->affected_rows()){
		    return $this->db->insert_id();
		}
		else{
			return null;
		}
	}
	
	/**
	 * 根据条件跟新单条数据...
	 */
	public function update($data) {
		$param = array(
			"page_name"=>$data['page_name'],
			"page_state"=>$data['page_state'],
		    "page_url" => $data['page_url'],
		    "page_desc"=>$data['page_desc'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('privilege', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
   /**
	 * 根据条件删除记录...
	 */
	public function delete($id) {
	    $data = array(
			'deleted_at' => date('Y-m-d H:i:s', time()),
		);
		
		$this->db->where('id', $id);
		$this->db->update('privilege', $data);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
   /**
	 * 按页查询记录  多结果查询（数组形式）  返回一个多维数组...
	 */
	public function selectPageData($searchkey, $offset, $num) {
	    //取总条数
	    if(!empty($searchkey) && $searchkey != ''){
		    $sql = "SELECT * FROM privilege WHERE page_name like '%".$searchkey."%' AND deleted_at is null";
	    }
	    else{
	    	$sql = "SELECT * FROM privilege WHERE deleted_at is null";
	    }
		$query = $this->db->query($sql);
		if ($query->num_rows() >= 0){
			$all = $query->result_array();
			$allCount = count($all);
		}
		else{
			return null;
		}
		
		if(!empty($searchkey) && $searchkey != ''){
		    $sql = "SELECT * FROM privilege WHERE page_name like '%".$searchkey."%' AND deleted_at is null ORDER BY id DESC LIMIT ".$offset.",".$num;
		}
		else{
			$sql = "SELECT * FROM privilege WHERE deleted_at is null ORDER BY id DESC LIMIT ".$offset.",".$num;
		}
		$query = $this->db->query($sql);
		if ($query->num_rows() >= 0){
			$return = $query->result_array();
		    return array("data"=>$return, "count"=>$allCount);
		}
		else{
			return null;
		}
	}
	
   /**
	 * 查询所有记录  多结果查询（数组形式）  返回一个多维数组...
	 */
	public function selectAllData() {
	    $sql = "SELECT * FROM privilege WHERE deleted_at is null ORDER BY id DESC";
		$query = $this->db->query($sql);
		if ($query->num_rows() >= 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
   /**
	 * 插入单条记录...
	 */
	public function insertRole($data) {
		$param = array(
			"name"=>$data['name'],
			"desc"=>$data['desc'],
		    "privilege_list" => $data['privilege_list'],
		);
			
		$this->db->insert('role', $param);
		if($this->db->affected_rows()){
		    return $this->db->insert_id();
		}
		else{
			return null;
		}
	}
	
	/**
	 * 根据条件跟新单条数据...
	 */
	public function updateRole($data) {
		$param = array(
			"name"=>$data['name'],
			"desc"=>$data['desc'],
		    "privilege_list" => $data['privilege_list'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('role', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
   /**
	 * 根据条件删除记录...
	 */
	public function deleteRole($id) {
	    $data = array(
			'deleted_at' => date('Y-m-d H:i:s', time()),
		);
		
		$this->db->where('id', $id);
		$this->db->update('role', $data);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
   /**
     * 查询角色数据
	 * 按页查询记录  多结果查询（数组形式）  返回一个多维数组...
	 */
	public function selectRolePageData($searchkey, $offset, $num) {
	    //取总条数
	    if(!empty($searchkey) && $searchkey != ''){
		    $sql = "SELECT * FROM role WHERE name like '%".$searchkey."%' AND deleted_at is null";
	    }
	    else{
	    	$sql = "SELECT * FROM role WHERE deleted_at is null";
	    }
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$all = $query->result_array();
			$allCount = count($all);
		}
		else{
			return null;
		}
		
		if(!empty($searchkey) && $searchkey != ''){
		    $sql = "SELECT * FROM role WHERE name like '%".$searchkey."%' AND deleted_at is null ORDER BY id DESC LIMIT ".$offset.",".$num;
		}
		else{
			$sql = "SELECT * FROM role WHERE deleted_at is null ORDER BY id DESC LIMIT ".$offset.",".$num;
		}
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array("data"=>$return, "count"=>$allCount);
		}
		else{
			return null;
		}
	}
	
   /**
	 * 查询所有角色...
	 */
	public function getAllRoleData() {
	    $sql = "SELECT * FROM role WHERE deleted_at is null ORDER BY id DESC";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function getRoleByKey() {
	    $sql = "SELECT * FROM role WHERE deleted_at is null AND name like '%领导%'";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function getRoleUserByRoleId($id) {
	    $sql = "SELECT * FROM admin WHERE deleted_at is null AND role_id = ".$id;
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
}