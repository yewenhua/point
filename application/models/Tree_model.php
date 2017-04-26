<?php
class Tree_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
	/**
	 * 插入单条记录...
	 */
	public function insert($data) {
		if(strpos($data['path'], '/') !== false){
			$pathArray = explode('/', $data['path']);
			$level = count($pathArray) + 1;
		}
		else{
			$level = 2;
		}
		
		$param = array(
			'path' => $data['path'],
		    'label' => $data['label'],
		    'level' => $level,
		    'is_root' => $data['is_root'],
		    'orderby' => $data['orderby'],
		    'is_open' => $data['is_open'],
		    'img_url' => $data['img_url']
		);
		
		$this->db->insert('tree', $param);
		if($this->db->affected_rows()){
			$extra_param = array(
			    "node_id"=>$this->db->insert_id(),
			);
			$data = array_merge($data, $extra_param);
			if($data['is_root']){
				$data['path'] = $this->db->insert_id();
			}
			else{
				$data['path'] = $data['path']."/".$this->db->insert_id();
			}
			
			if($this->update($data)){
				return $data;
			}
			else{
				return null;
			}
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
			'path' => $data['path'],
		    'label' => $data['label'],
		    'orderby' => $data['orderby'],
		    'img_url' => $data['img_url'],
		    'is_open' => $data['is_open'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('node_id', $data['node_id']);
		$this->db->update('tree', $param);
		
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
		$time = date('Y-m-d H:i:s', time());
		$sql = "UPDATE tree SET deleted_at = '".$time."' WHERE path LIKE '".$id."%'";
		$query = $this->db->query($sql);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	/**
	 * 查询单条记录  数组形式...
	 */
	public function selectRowData($id) {
		$param = array('node_id' => $id);
		$query = $this->db->select('*')->from('tree')->where($param)->get();
	    if ($query->num_rows() > 0){
		    return $query->row_array();
		}
		else{
			return null;
		}
	}
	
	/**
	 * 查询所有记录  多结果查询（数组形式）  返回一个多维数组...
	 */
	public function selectArrayData() {
		$sql = 'SELECT * FROM tree WHERE deleted_at is null ORDER BY orderby ASC';
		$query = $this->db->query($sql);
		if ($query->num_rows() >= 0){
		    return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function select_tree_second_level() {
		$sql = 'SELECT * FROM tree WHERE deleted_at is null AND level = 2 ORDER BY orderby ASC';
		$query = $this->db->query($sql);
		if ($query->num_rows() >= 0){
		    return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function select_tree_by_path($path) {
	    $sql = "SELECT * FROM tree WHERE deleted_at is null AND is_root != 1 AND path like '%".$path."%' ORDER BY orderby ASC";
		$query = $this->db->query($sql);
		if ($query->num_rows() >= 0){
		    return $query->result_array();
		}
		else{
			return null;
		}
	}
	
}