<?php
class Article_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
   /**
	 * 插入单条记录...
	 */
	public function insertRow($data) {
		$param = array(
			'title' => $data['title'],
		    "sort_id"=> $data['sort_id'],
		    "content"=> $data['content'],
		    "abstracts"=> $data['abstracts'],
		    "img_url"=> $data['img_url']
		);
			
		$this->db->insert('article', $param);
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
			'title' => $data['title'],
		    "content"=> $data['content'],
		    "abstracts"=> $data['abstracts'],
		    "img_url"=> $data['img_url'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('article', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function selectMaxSortRow() {
		$sql = "SELECT * FROM article WHERE deleted_at is null ORDER BY sort_id DESC LIMIT 0,1";
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
	public function changeSortToNew($id, $new_sort_id) {
		$param = array(
		    'sort_id' => $new_sort_id,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $id);
		$this->db->update('article', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
    public function selectDataByOffset($offset){
	    $sql = "SELECT * FROM article WHERE deleted_at is null ORDER BY sort_id DESC LIMIT ".$offset.",1";
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
		$this->db->update('article', $paramFirst);
		$this->db->where('id', $second_id);
		$this->db->update('article', $paramSecond);
		
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
	
    public function getNewsList($offset, $num){	
	    $where = 'deleted_at is null';

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);

		$queryAll = $this->db->get('article');
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['total'];
		}
		else{
			$count = 0;
		}
		
		//分页
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('sort_id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('article');
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $count,
		        "data" => $return
		    );
		}
		else{
			return null;
		}
	}
	
	public function selectDataById($id) {
		$where = array(
			"id" => $id
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('article');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
}