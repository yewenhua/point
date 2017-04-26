<?php
class System_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * 插入单条记录...
	 */
     public function insert($data) {
		$param = array(
			"title"=>$data['title'],
		    "author"=>$data['author'],
		    "privilege_key"=>$data['privilege_key'],
		    "site_name"=>$data['site_name'],
			"site_www"=>$data['site_www'],
			"keywords"=>$data['keywords'],
			"description"=>$data['description']
		);
			
		$this->db->insert('system', $param);
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
			"title"=>$data['title'],
		    "author"=>$data['author'],
		    "privilege_key"=>$data['privilege_key'],
		    "site_name"=>$data['site_name'],
			"site_www"=>$data['site_www'],
			"keywords"=>$data['keywords'],
			"description"=>$data['description'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('system', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getSystemData() {		
		$sql = "SELECT * FROM system WHERE deleted_at is null";
		$query = $this->db->query($sql);
	    if ($query->num_rows() > 0){
	    	return $query->row_array();
		}
		else{
			return null;
		}
	}
	
	public function update_privilege($id, $code) {
		$param = array(
		    "privilege_key"=>$code,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $id);
		$this->db->update('system', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
}