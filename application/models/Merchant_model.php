<?php
class Merchant_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
    public function dologin($data) {
		$newPwd = $this->password($data['password']);
		$where = array(
			"mobile" => $data['name'],
			"password" => $newPwd,
		    "is_company" => 1
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('member');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
   /**
	 * 修改密码...
	 */
	public function chgpwd($data) {
		$param = array(
			'password' => $this->password($data['newpassword']),
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $data['id']);
		$this->db->where('password', $this->password($data['password']));
		$this->db->update('member', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	
	/**
	 * password...
	 */
	public function password($original){
		$string = "goodluck";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
	}
	
	public function selectPageData($user_id, $status, $time, $searchkey, $offset, $num){	
	    $where = 'orders.deleted_at is null AND goods.company_id = '.$user_id;

		//总数
		$this->db->select('count(orders.id) AS total');
		$this->db->from('orders');
		$this->db->join('member', 'member.id = orders.user_id');
		$this->db->join('goods', 'goods.id = orders.goods_id');
		
		if($status != 99){
			$status_where = array(
				"status" => $status
			);
			$this->db->where($status_where);
		}
		$this->db->where($where);
		
	    if($time){
	    	$begin = $time.' 00:00:00';
	    	$end = $time.' 23:59:59';
	    	$time_where = " orders.created_at >= '".$begin."' AND orders.created_at <= '".$end."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('orders.order_id', $searchkey);
			$this->db->or_like('orders.goods_name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		
		
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['total'];
		}
		else{
			$count = 0;
		}
		
		//分页
		$this->db->select('orders.*, member.name, member.mobile');
		$this->db->from('orders');
		$this->db->join('member', 'member.id = orders.user_id');
		$this->db->join('goods', 'goods.id = orders.goods_id');
		
	    if($status != 99){
			$status_where = array(
				"status" => $status
			);
			$this->db->where($status_where);
		}
		$this->db->where($where);
	    if($time){
	    	$begin = $time.' 00:00:00';
	    	$end = $time.' 23:59:59';
	    	$time_where = " orders.created_at >= '".$begin."' AND orders.created_at <= '".$end."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('orders.order_id', $searchkey);
			$this->db->or_like('orders.goods_name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('orders.id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get();
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
	
    public function allOrderStatusData($user_id) {
        $where = 'orders.deleted_at is null';
        $user_where = array(
			"goods.company_id" => $user_id
		);
		
		$this->db->select('orders.status, orders.created_at');
		$this->db->from('orders');
		$this->db->join('goods', 'goods.id = orders.goods_id');
		$this->db->where($where);
		$this->db->where($user_where);
	
		$this->db->order_by('orders.id', 'DESC');
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	/**
	 * 过去一个月现金流
	 */
	public function welcomeOrderData($user_id) {
		$start = date('Y-m-d', time()-30*24*60*60).' 00:00:00';
	    $end = date('Y-m-d', time()-24*60*60).' 23:59:59';
	    
	    $where = "orders.deleted_at is null AND orders.created_at >= '".$start."' AND orders.created_at <= '".$end."'";
        $user_where = array(
			"goods.company_id" => $user_id
		);
		$status_where = array(1, 2, 3);
		
		$this->db->select('orders.created_at, orders.total_cash_price');
		$this->db->from('orders');
		$this->db->join('goods', 'goods.id = orders.goods_id');
		$this->db->where($where);
		$this->db->where($user_where);
		$this->db->where_in('orders.status', $status_where);
		$query = $this->db->get();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
    public function allCashData($user_id) {
		$where = "orders.deleted_at is null";
        $user_where = array(
			"goods.company_id" => $user_id
		);
		
		$this->db->select_sum('orders.total_cash_price');
		$this->db->from('orders');
		$this->db->join('goods', 'goods.id = orders.goods_id');
		$this->db->where($where);
		$this->db->where($user_where);
		$query = $this->db->get();
		if ($query->num_rows() > 0){
			$return = $query->row_array();
			return round($return['total_cash_price'], 2);
		}
		else{
			return 0;
		}
	}
	
    public function getWaitSendData($user_id) {
        $where = 'orders.deleted_at is null AND orders.status = 1';
		$this->db->select('orders.*');
		$this->db->from('orders');
		$this->db->join('goods', 'goods.id = orders.goods_id');
		$this->db->where($where);
		$user_where = array(
			"goods.company_id" => $user_id
		);
		$this->db->where($user_where);
	
		$this->db->order_by('orders.id', 'ASC');
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function outputOrderExcelData($user_id, $status, $time, $searchkey){	
	    $where = 'orders.deleted_at is null AND goods.company_id = '.$user_id;

		//总数
		$this->db->select('orders.*, member.name, member.mobile');
		$this->db->from('orders');
		$this->db->join('member', 'member.id = orders.user_id');
		$this->db->join('goods', 'goods.id = orders.goods_id');
		
		if($status != 99){
			$status_where = array(
				"status" => $status
			);
			$this->db->where($status_where);
		}
		$this->db->where($where);
		
	    if($time){
	    	$begin = $time.' 00:00:00';
	    	$end = $time.' 23:59:59';
	    	$time_where = " orders.created_at >= '".$begin."' AND orders.created_at <= '".$end."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('orders.order_id', $searchkey);
			$this->db->or_like('orders.goods_name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		
		
		$query = $this->db->get();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
}