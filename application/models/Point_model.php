<?php
class Point_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function do_repeat($repeatInfo, $shoot_point, $future_point, $active_time, $user_id, $order_id) {
    	$shoot_param = array(
		    "multiple" => $repeatInfo['rate'],
    	    "day" => $repeatInfo['day'],
		    "shoot_point" => $shoot_point,
	    	"future_point" => $future_point,
	    	"active_time" => $active_time,
    	    "type" => 1,
	    	"user_id" => $user_id,
    	    "order_id" => $order_id,
    	    "auto_to_consume" => $repeatInfo['auto_to_consume']
		);
		
		$useable_param = array(
    	    "type" => 1,
		    "point" => $shoot_point,
	    	"user_id" => $user_id,
		    "order_id" => $order_id
		);
		
		//事务
        $this->db->trans_begin();
        $flag = false;
		$memberSql = "UPDATE member SET useable_point = useable_point - ".$shoot_point.",  wait_point = wait_point + ".$future_point." WHERE id = ".$user_id." AND useable_point >= ".$shoot_point;;
		$this->db->query($memberSql);
		if($this->db->affected_rows()){
			$flag = true;
			$rest_point = $repeatInfo['limit_point'] - $repeatInfo['already_shot_point'] - $shoot_point;
			if($rest_point > 0){
		        $recordSql = "UPDATE repeat_info SET already_shot_point = already_shot_point + ".$shoot_point." WHERE id = ".$repeatInfo['id'];
			}
			else{
				$recordSql = "UPDATE repeat_info SET already_shot_point = already_shot_point + ".$shoot_point.", status = 1 WHERE id = ".$repeatInfo['id'];
			}
	        $this->db->insert('shoot_record', $shoot_param);
	        $this->db->insert('useable_record', $useable_param);
	        $this->db->query($recordSql);
		}
		
	    if ($this->db->trans_status() === FALSE || !$flag)
		{
			// 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
			$this->db->trans_rollback();
			return null;
		}
		else
		{
			$this->db->trans_commit();
		    return true;
		}
	}
	
	public function do_able_consume($order_id, $point, $user_id) {
		$useable_param = array(
    	    "type" => 2, // 1复投  2可用转消费
		    "point" => $point,
	    	"user_id" => $user_id,
		    "order_id" => $order_id
		);

		$consume_param = array(
    	    "type" => 1,  //1可用积分转消费积分 2积分转让 3下线充值返利 4兑换商品
		    "point" => $point,
		    "reason" => 2, //1转出  2转入
	    	"user_id" => $user_id,
		    "order_id" => $order_id
		);
		
		$memberSql = "UPDATE member SET useable_point = useable_point - ".$point.", consume_point = consume_point + ".$point." WHERE id = ".$user_id;
		
	    //事务
        $this->db->trans_begin();
        $this->db->insert('useable_record', $useable_param);
        $this->db->insert('consume_record', $consume_param);
        $this->db->query($memberSql);
        
	    if ($this->db->trans_status() === FALSE)
		{
			// 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
			$this->db->trans_rollback();
			return null;
		}
		else
		{
			$this->db->trans_commit();
		    return true;
		}
	}
	
	public function useable_data_by_page($user_id, $page, $num) {	
		$offset = ($page - 1) * $num;
	    $where = 'deleted_at is null';
	    $and_where = array(
			"user_id" => $user_id
		);

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('useable_record');
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
		$this->db->where($and_where);

		$this->db->order_by('id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('useable_record');
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
	
	public function do_transfer_consume($point, $original_user_id, $target_user_id, $order_id) {
		$consume_param_one = array(
    	    "type" => 2, //1可用积分转消费积分 2积分转让 3下线充值返利 4兑换商品
    	    "reason" => 2,  //1转出  2转入
		    "point" => $point,
	    	"user_id" => $target_user_id,
		    "order_id" => $order_id
		);
		
		$consume_param_two = array(
    	    "type" => 2, //1可用积分转消费积分 2积分转让 3下线充值返利 4兑换商品
    	    "reason" => 1,  //1转出  2转入
		    "point" => $point,
	    	"user_id" => $original_user_id,
		    "order_id" => $order_id
		);
		
		$transfer_param = array(
    	    "original_user_id" => $original_user_id,
    	    "target_user_id" => $target_user_id,
		    "point" => $point,
	    	"order_id" => $order_id
		);
		
		$originalMemberSql = "UPDATE member SET consume_point = consume_point - ".$point." WHERE id = ".$original_user_id;
		$targetMemberSql = "UPDATE member SET consume_point = consume_point + ".$point." WHERE id = ".$target_user_id;
		
	    //事务
        $this->db->trans_begin();
        $this->db->insert('consume_record', $consume_param_one);
        $this->db->insert('consume_record', $consume_param_two);
        $this->db->insert('transfer_record', $transfer_param);
        $this->db->query($originalMemberSql);
        $this->db->query($targetMemberSql);
        
	    if ($this->db->trans_status() === FALSE)
		{
			// 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
			$this->db->trans_rollback();
			return null;
		}
		else
		{
			$this->db->trans_commit();
		    return true;
		}
	}
	
	public function consume_data_by_page($user_id, $page, $num) {	
		$offset = ($page - 1) * $num;
	    $where = 'deleted_at is null';
	    $and_where = array(
			"user_id" => $user_id
		);

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('consume_record');
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
		$this->db->where($and_where);

		$this->db->order_by('id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('consume_record');
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
	
	public function wait_data_by_page($user_id, $page, $num) {	
		$offset = ($page - 1) * $num;
	    $where = 'deleted_at is null';
	    $and_where = array(
			"user_id" => $user_id
		);

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('shoot_record');
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
		$this->db->where($and_where);

		$this->db->order_by('id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('shoot_record');
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
	
	public function share_data_by_page($user_id, $page, $num) {	
		$offset = ($page - 1) * $num;
	    $where = 'deleted_at is null';
	    $and_where = array(
			"user_id" => $user_id
		);

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('share_record');
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
		$this->db->where($and_where);

		$this->db->order_by('id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('share_record');
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
	
	public function exchange_data_by_page($user_id, $page, $num) {	
		$offset = ($page - 1) * $num;
	    $where = 'deleted_at is null';
	    $and_where = array(
			"user_id" => $user_id
		);

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('exchange_record');
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
		$this->db->where($and_where);

		$this->db->order_by('id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('exchange_record');
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
	
	public function get_clear_wait_point() {
		$now = date('Y-m-d H:i:s');
	    $where = "deleted_at is null AND active_time < '".$now."' ";
	    $and_where = array(
			"status" => 0
		);

		//总数
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($and_where);

		$query = $this->db->get('shoot_record');
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function do_clear_wait_point($data) {
		//事务
        $this->db->trans_begin();
    	$shoot_param = array(
    	    "status" => 1,
    	    "updated_at"=>date('Y-m-d H:i:s')
		);
		
		$useable_param = array(
    	    "type" => 3,
		    "point" => $data['future_point'],
	    	"user_id" => $data['user_id'],
		    "order_id" => $data['order_id']
		);
		
		$memberSql = "UPDATE member SET useable_point = useable_point + ".$data['future_point'].",  wait_point = wait_point - ".$data['future_point']." WHERE id = ".$data['user_id'];
		if($data['auto_to_consume'] == 1){
			//可用自动转消费
			$useable_auto_param = array(
	    	    "type" => 2, // 1复投  2可用转消费
			    "point" => $data['future_point'],
		    	"user_id" => $data['user_id'],
			    "order_id" => $data['order_id']
			);
	
			$consume_auto_param = array(
	    	    "type" => 1,  //1可用积分转消费积分 2积分转让 3下线充值返利 4兑换商品
			    "point" => $data['future_point'],
			    "reason" => 2, //1转出  2转入
		    	"user_id" => $data['user_id'],
			    "order_id" => $data['order_id']
			);
			
			$memberAutoSql = "UPDATE member SET useable_point = useable_point - ".$data['future_point'].", consume_point = consume_point + ".$data['future_point']." WHERE id = ".$data['user_id'];
			$this->db->insert('useable_record', $useable_auto_param);
	        $this->db->insert('consume_record', $consume_auto_param);
	        $this->db->query($memberAutoSql);
		}
		
        $this->db->where('id', $data['id']);
        $this->db->update('shoot_record', $shoot_param);
        $this->db->insert('useable_record', $useable_param);
        $this->db->query($memberSql);
        
	    if ($this->db->trans_status() === FALSE)
		{
			// 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
			$this->db->trans_rollback();
			return null;
		}
		else
		{
			$this->db->trans_commit();
		    return true;
		}
	}
	
	public function getWaitPointById($id) {
		$where = array(
			"id" => $id
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('shoot_record');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
    public function getFirstShootByUid($uid) {
		$where = array(
			"user_id" => $uid
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->order_by('active_time', 'ASC')
		    ->get('shoot_record');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
    public function repeat_data_by_page($user_id, $page, $num, $type) {
        $offset = ($page - 1) * $num;
        if($type == 1){
        	//进行中
	        $where = "deleted_at is null AND begin_time <= '".date('Y-m-d H:i:s')."' AND end_time >= '".date('Y-m-d H:i:s')."' ";
        }
        elseif($type == 2){
        	//已过期
        	$where = "deleted_at is null AND end_time < '".date('Y-m-d H:i:s')."' ";
        }
        elseif($type == 3){
        	//待开启
        	$where = "deleted_at is null AND begin_time > '".date('Y-m-d H:i:s')."' ";
        }
        else{
        	$where = "";
        }
        
	    $and_where = array(
			"user_id" => $user_id
		);

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('repeat_info');
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
		$this->db->where($and_where);

		$this->db->order_by('begin_time', 'ASC');
        if($type == 1){
			$this->db->order_by('status', 'ASC');
		}
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('repeat_info');
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
	
	public function getRepeatById($id) {
		$where = array(
			"id" => $id
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('repeat_info');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function getMerchantNotRepeatData($start, $end) {
		$type_array = array(4, 5);
		$where = array(
			"status" => 0
		);
		$and_where = "deleted_at is null AND created_at >= '".$start."' AND created_at <= '".$end."' ";
		
		$query = $this->db->select('sum(point) as point, user_id')
		    ->where($where)
		    ->where($and_where)
		    ->where_in('type', $type_array)
		    ->group_by("user_id")
		    ->get('useable_record');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function generate_merchant_repeat($order_id, $user_id, $point, $merchant_repeat_rate_arr, $merchant_repeat_period_arr, $start, $end) {
	    $repeatParam = array();
    	for($i=0; $i<count($merchant_repeat_rate_arr); $i++){
    		if($i == 0){
    			$begin_time = time();
	    		$end_time = $begin_time + $merchant_repeat_period_arr[$i] * 24 * 60 * 60;
	    		$limit_point = $point;
    		}
    		else{
    			$begin_time = $end_time + 1;
    			$end_time = $begin_time + $merchant_repeat_period_arr[$i] * 24 * 60 * 60;
    			$limit_point = $limit_point * $merchant_repeat_rate_arr[$i-1];
    		}
    		
    		$repeatParam[] = array(
    		    "order_id" => $order_id,
				"user_id" => $user_id,
    		    "rate" => $merchant_repeat_rate_arr[$i],
    		    "day" => $merchant_repeat_period_arr[$i],
			    "already_shot_point" => 0,
    		    "type" => 3, //1报单 2升级 3商家 4补偿
			    "limit_point" => $limit_point,
	    		"begin_time" => date('Y-m-d H:i:s', $begin_time),
	    		"end_time" => date('Y-m-d H:i:s', $end_time)
			);
    	}
		
		$merchantSql = "UPDATE useable_record SET status = 1 WHERE user_id = ".$user_id." AND (type = 4 OR type = 5) AND status = 0 AND created_at >= '".$start."' AND created_at <= '".$end."' ";
		
	    //事务
        $this->db->trans_begin();
	    $length = count($repeatParam);
        $lastIndex = $length - 1;
        for($i=0; $i<$length; $i++){
        	$repeatParam[$i]['round'] = $i + 2;
        	if($i == $lastIndex){
        		$repeatParam[$i]['auto_to_consume'] = 1;
        	}
        	$this->db->insert('repeat_info', $repeatParam[$i]);
        }
        
        $this->db->query($merchantSql);
        
	    if ($this->db->trans_status() === FALSE)
		{
			// 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
			$this->db->trans_rollback();
			return null;
		}
		else
		{
			$this->db->trans_commit();
		    return true;
		}
	}
}
	