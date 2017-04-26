<?php
class Setting_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
   /**
	 * 插入单条记录...
	 */
	public function insertSchedule($data) {
		$param = array(
			"multiple" => $data['multiple'],
		    "day"=> $data['day'],
			"dead_time"=> $data['deadtime'],
			"stop_time"=> $data['stoptime'],
		    "is_open"=> $data['is_open']
		);
			
		$this->db->insert('schedule', $param);
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
	public function updateSchedule($data) {
		$param = array(
			"multiple" => $data['multiple'],
		    "day"=> $data['day'],
		    "dead_time"=> $data['deadtime'],
			"stop_time"=> $data['stoptime'],
		    "is_open"=> $data['is_open'],
		    "updated_at" => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('schedule', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getScheduleData() {		
		$where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->get('schedule');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function getConfigDeclaration() {
        $where = 'deleted_at is null';
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('id', 'DESC');
			
		$query = $this->db->get('config_declaration');
	    if ($query->num_rows() > 0){
			return $query->row_array();
		}
		else{
			return null;
		}
	}
	
   /**
	 * 插入单条记录...
	 */
	public function insertCommisionSetting($data) {
		$param = array(
			"junior_first" => $data['junior_first'],
		    "junior_second"=> $data['junior_second'],
			"junior_third"=> $data['junior_third'],
			"middle_first"=> $data['middle_first'],
			"middle_second"=> $data['middle_second'],
			"middle_third"=> $data['middle_third'],
			"advanced_first"=> $data['advanced_first'],
			"advanced_second"=> $data['advanced_second'],
			"advanced_third"=> $data['advanced_third']
		);
			
		$this->db->insert('commision_setting', $param);
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
	public function updateCommisionSetting($data) {
		$param = array(
			"junior_first" => $data['junior_first'],
		    "junior_second"=> $data['junior_second'],
			"junior_third"=> $data['junior_third'],
			"middle_first"=> $data['middle_first'],
			"middle_second"=> $data['middle_second'],
			"middle_third"=> $data['middle_third'],
			"advanced_first"=> $data['advanced_first'],
			"advanced_second"=> $data['advanced_second'],
			"advanced_third"=> $data['advanced_third'],
		    "updated_at" => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('commision_setting', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getCommisionSetting() {		
		$where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->get('commision_setting');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
}