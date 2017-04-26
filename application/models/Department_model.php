<?php
class Department_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * 按页获取记录  返回一个多维数组...
	 */
	public function selectPageData($searchkey, $offset, $num) {
	    //取总条数
	    //按条件搜索
	    if(!empty($searchkey) && $searchkey != ''){
	    	$sql = "SELECT count(*) as count FROM department WHERE name like '%".$searchkey."%' AND deleted_at is null";
	    }
	    else{
	    	$sql = "SELECT count(*) as count FROM department WHERE deleted_at is null";
	    }

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$res = $query->row_array();
			$allCount = $res['count'];
		}
		else{
			return null;
		}
		
		//取页数据
	    //按条件搜索会员
		if(!empty($searchkey) && $searchkey != ''){
			$sql = "SELECT * FROM department WHERE name like '%".$searchkey."%' AND deleted_at is null ORDER BY sort_id DESC LIMIT ".$offset.",".$num;
		}
		else{
			$sql = "SELECT * FROM department WHERE deleted_at is null ORDER BY sort_id DESC LIMIT ".$offset.",".$num;
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
	
	public function selectAllData() {
		$sql = "SELECT * FROM department WHERE deleted_at is null ORDER BY is_lock ASC, sort_id DESC";
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
	public function selectDataById($id) {
		$sql = "SELECT * FROM department WHERE deleted_at is null AND id = ".$id;
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
   /**
	 * 插入单条记录...
	 */
	public function insertRow($data) {
		$param = array(
			'name' => $data['name'],
		    "sort_id"=> $data['sort_id'],
		    "is_lock"=> $data['is_lock'],
		);
			
		$this->db->insert('department', $param);
		if($this->db->affected_rows()){
		    return $this->db->insert_id();
		}
		else{
			return false;
		}
	}
	
	/**
	 * 根据条件跟新单条数据...
	 */
	public function updateRow($data) {
		$param = array(
			'name' => $data['name'],
		    "is_lock"=> $data['is_lock'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('department', $param);
		
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
	public function deleteRow($id) {
	    $data = array(
			'deleted_at' => date('Y-m-d H:i:s', time()),
		);
		
		$this->db->where('id', $id);
		$this->db->update('department', $data);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function deleteBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('department', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function lockBatch($idlist) {
		$param = array(
		    "is_lock"=> 1,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('department', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function selectDataByOffset($offset){
	    $sql = "SELECT * FROM department WHERE deleted_at is null ORDER BY sort_id DESC LIMIT ".$offset.",1";
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
	public function changeSortEachother($first_id, $first_sort_id, $second_id, $second_sort_id) {
		$paramFirst = array(
		    "sort_id"=>$second_sort_id,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$paramSecond = array(
		    "sort_id"=>$first_sort_id,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->trans_begin();
		$this->db->where('id', $first_id);
		$this->db->update('department', $paramFirst);
		$this->db->where('id', $second_id);
		$this->db->update('department', $paramSecond);
		
	    //事务
	    if ($this->db->trans_status() === FALSE)
		{
			// 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return true;
		}
	}
	
	public function selectMaxSortRow() {
		$sql = "SELECT * FROM department WHERE deleted_at is null ORDER BY sort_id DESC LIMIT 0,1";
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return;
		}
		else{
			return null;
		}
	}
}