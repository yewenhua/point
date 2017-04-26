<?php
class Crontab_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
    public function smsTemplate() {
		$where = "deleted_at is null";

		//总数
		$this->db->select('*');
		$this->db->where($where);

		$query = $this->db->get('sms_template');
		if ($query->num_rows() >= 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function getChargeData(){
	    $where = "deleted_at is null";
	    $and_where = array(
			"is_send" => 0, //未发送
	        "status" => 1   //已支付
		);

		//总数
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($and_where);

		$query = $this->db->get('recharge_record');
		if ($query->num_rows() >= 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function update_charge_send_status($id, $value) {
		$param = array(
			'is_send' => $value,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $id);
		$this->db->update('recharge_record', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	
    public function getClearData(){
        $where = "deleted_at is null";
	    $and_where = array(
			"is_send" => 0, //未发送
	        "status" => 1   //已生效
		);

		//总数
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($and_where);

		$query = $this->db->get('shoot_record');
		if ($query->num_rows() >= 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function update_clear_send_status($id, $value) {
		$param = array(
			'is_send' => $value,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $id);
		$this->db->update('shoot_record', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	
    public function getRepeatOpenData(){
    	$now = date('Y-m-d H:i:s', time());
        $where = "deleted_at is null AND begin_time <= '".$now."' AND end_time >= '".$now."' ";
	    $and_where = array(
			"is_open_send" => 0, //未发送
	        "status" => 0        //有剩余额度
		);

		//总数
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($and_where);

		$query = $this->db->get('repeat_info');
		if ($query->num_rows() >= 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
    public function update_repeat_send_open_status($id, $value) {
		$param = array(
			'is_open_send' => $value,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $id);
		$this->db->update('repeat_info', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	
    public function getRepeatWeekData(){
        $over_time = date('Y-m-d H:i:s', time() + 7 * 24 * 60 * 60);
        $where = "deleted_at is null AND end_time <= '".$over_time."' ";
	    $and_where = array(
			"is_week_send" => 0, //未发送
	        "status" => 0        //有剩余额度
		);

		//总数
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($and_where);

		$query = $this->db->get('repeat_info');
		if ($query->num_rows() >= 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
    public function update_repeat_send_week_status($id, $value) {
		$param = array(
			'is_week_send' => $value,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $id);
		$this->db->update('repeat_info', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	
    public function getRepeatDayData(){
        $over_time = date('Y-m-d H:i:s', time() + 24 * 60 * 60);
        $where = "deleted_at is null AND end_time <= '".$over_time."' ";
	    $and_where = array(
			"is_day_send" => 0,  //未发送
	        "status" => 0        //有剩余额度
		);

		//总数
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($and_where);

		$query = $this->db->get('repeat_info');
		if ($query->num_rows() >= 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
    public function update_repeat_send_day_status($id, $value) {
		$param = array(
			'is_day_send' => $value,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $id);
		$this->db->update('repeat_info', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	
    public function getOrderSendData(){
        $where = "deleted_at is null";
	    $and_where = array(
			"is_send" => 0, //未发送
	        "status" => 2   //已发货
		);

		//总数
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($and_where);

		$query = $this->db->get('orders');
		if ($query->num_rows() >= 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
    public function update_order_send_status($id, $value) {
		$param = array(
			'is_send' => $value,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $id);
		$this->db->update('orders', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	
	public function getOrderPayData(){
        $where = "deleted_at is null";
	    $and_where = array(
			"is_pay_send" => 0, //未发送
	        "status" => 1   //已支付
		);

		//总数
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($and_where);

		$query = $this->db->get('orders');
		if ($query->num_rows() >= 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
    public function update_order_send_pay_status($id, $value) {
		$param = array(
			'is_pay_send' => $value,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $id);
		$this->db->update('orders', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
}