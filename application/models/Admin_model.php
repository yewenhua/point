<?php
class Admin_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
   /**
	 * login...管理员
	 */
	public function signin($data) {
		$newPwd = $this->password($data['password']);
		$sql = "SELECT * FROM admin WHERE deleted_at is null AND (name = '".$data['name']."' OR mobile = '".$data['name']."' OR id = '".$data['name']."') AND password = '".$newPwd."'";
		$query = $this->db->query($sql);
	    if ($query->num_rows() > 0){
	    	$return = $query->row_array();
	    	
	    	//角色
	        $sqlRole = "SELECT * FROM role WHERE id = ".$return['role_id'];
			$queryRole = $this->db->query($sqlRole);
			if ($queryRole->num_rows() > 0){
				$role = $queryRole->row_array();
				$return['role_name'] = $role['name'];
				$return['privilege_list'] = $role['privilege_list'];
				$return['dep_name'] = '';
				
				//部门
				if($return['dep_id']){
					$return['dep_list'] = array();
					$sqlDep = "SELECT * FROM department WHERE id IN (".$return['dep_id'].")";
					$queryDep = $this->db->query($sqlDep);
					if ($queryDep->num_rows() > 0){
						$dep = $queryDep->result_array();
						foreach($dep as $item){
							if($return['dep_name'] == ''){
							    $return['dep_name'] = $item['name'];
							}
							else{
								$return['dep_name'] .= ','.$item['name'];
							}
							
							$return['dep_list'][] = array(
							    'id'=>$item['id'], 
							    'name'=>$item['name']
							);
						}
					}
				}
				return $return;
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
	 * password...
	 */
	public function password($original){
		$string = "goodluck";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
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
		$this->db->update('admin', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	
   /**
	 * 插入单条记录...
	 */
	public function insertAdmin($data) {
		$param = array(
			"name"=>$data['name'],
			"mobile"=>$data['mobile'],
		    "password" => $this->password($data['password']),
		    "role_id" => $data['role_id'],
		    "dep_id" => $data['dep_id'],
		    "is_lock" => $data['is_lock']
		);
			
		$this->db->insert('admin', $param);
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
	public function updateAdmin($data) {
		$param = array(
			"name"=>$data['name'],
			"mobile"=>$data['mobile'],
		    "role_id" => $data['role_id'],
		    "dep_id" => $data['dep_id'],
		    "is_lock" => $data['is_lock'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		if($data['password'] != '' && $data['password'] != null && $data['password'] != false){
			$extra_param = array(
			    "password"=>$this->password($data['password']),
			);
			$param = array_merge($param, $extra_param);
		}
		
		$this->db->where('id', $data['id']);
		$this->db->update('admin', $param);
		
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
		$this->db->update('admin', $data);
		
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
	public function selectAdminPageData($searchkey, $offset, $num) {
	    //取总条数
	    if(!empty($searchkey) && $searchkey != ''){
		    $sql = "SELECT * FROM admin WHERE (name like '%".$searchkey."%' OR mobile like '%".$searchkey."%' OR id like '%".$searchkey."%') AND deleted_at is null";
	    }
	    else{
	    	$sql = "SELECT * FROM admin WHERE deleted_at is null";
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
		    $sql = "SELECT * FROM admin WHERE (name like '%".$searchkey."%' OR mobile like '%".$searchkey."%' OR id like '%".$searchkey."%') AND deleted_at is null ORDER BY id DESC LIMIT ".$offset.",".$num;
		}
		else{
			$sql = "SELECT * FROM admin WHERE deleted_at is null ORDER BY id DESC LIMIT ".$offset.",".$num;
		}
		$query = $this->db->query($sql);
		if ($query->num_rows() >= 0){
			$return = $query->result_array();
			foreach($return as $key=>$item){
				$sql = "SELECT * FROM role WHERE id = ".$item['role_id'];
				$query = $this->db->query($sql);
				$return[$key]['role_name'] = '';
				if ($query->num_rows() >= 0){
					$role = $query->row_array();
					$return[$key]['role_name'] = $role['name'];
				}
			}
		    return array("data"=>$return, "count"=>$allCount);
		}
		else{
			return null;
		}
	}
	
	public function selectDataById($id) {
		$sql = "SELECT * FROM admin WHERE deleted_at is null AND id = ".$id;
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
	public function selectLastRow() {
		$sql = "SELECT * FROM admin WHERE deleted_at is null ORDER BY id DESC LIMIT 0,1";
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
	public function chgPersonal($data) {
		$param = array(
			'mobile' => $data['mobile'],
		    'name' => $data['name'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $data['id']);
		$this->db->update('admin', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	
	public function isExist($data) {
		if($data['name']){
	        $sql = "SELECT * FROM admin WHERE deleted_at is null AND name = '".$data['name']."'";
		}
		else{
			$sql = "SELECT * FROM admin WHERE deleted_at is null AND mobile = '".$data['mobile']."'";
		}
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
		    $data = $query->row_array();
		    return $data;
		}
		else{
			return null;
		}
	}
	
    public function allBannerData() {
        $where = 'deleted_at is null';
		$this->db->select('*');
		$this->db->where($where);
	
		$this->db->order_by('order_id', 'ASC');
		
		$query = $this->db->get('banner');
	    if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
   /**
	 * 根据条件删除记录...
	 */
	public function deleteBanner($id) {
	    $data = array(
			'deleted_at' => date('Y-m-d H:i:s', time()),
		);
		
		$this->db->where('id', $id);
		$this->db->update('banner', $data);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
   /**
	 * 插入单条记录...
	 */
	public function insertBanner($data) {
		$param = array(
			'name' => $data['name'],
		    "url"=> $data['url'],
		    "link"=> $data['link'],
		    "order_id"=> $data['order_id'],
		);
			
		$this->db->insert('banner', $param);
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
	public function updateBanner($data) {
		$param = array(
			'name' => $data['name'],
		    "link"=> $data['link'],
		    "order_id"=> $data['order_id'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
	    if($data['url']){
			$extra_param = array(
			    "url"=>$data['url'],
			);
			$param = array_merge($param, $extra_param);
		}
		
		$this->db->where('id', $data['id']);
		$this->db->update('banner', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getCashPageData($userid, $status, $start, $end, $searchkey, $offset, $num){
	    $where = 'cash_record.deleted_at is null';

		//总数
	    if($status == 99){
	    	//全部状态
	    	//正值
			$this->db->select('count(cash_record.id) AS total, sum(cash_record.money) as money');
			$this->db->from('cash_record');
			$this->db->join('member', 'member.id = cash_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(
				"cash_record.type" => 4 //退款
			);
			$this->db->where($status_where);
			
		    if($userid != 'empty'){
				$user_where = array(
					"cash_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " cash_record.created_at >= '".$begin."' AND cash_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('cash_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
	        if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				$plusCount = $data['total'];
				$plusMoney = $data['money'];
			}
			else{
				$plusCount = 0;
				$plusMoney = $data['money'];
			}
			
			//负值
	        $this->db->select('count(cash_record.id) AS total, sum(cash_record.money) as money');
			$this->db->from('cash_record');
			$this->db->join('member', 'member.id = cash_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(1, 2, 3);
			$this->db->where_in('cash_record.type', $status_where);
			
		    if($userid != 'empty'){
				$user_where = array(
					"cash_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " cash_record.created_at >= '".$begin."' AND cash_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('cash_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
	        if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				$minusCount = $data['total'];
				$minusMoney = $data['money'];
			}
			else{
				$minusCount = 0;
				$minusMoney = 0;
			}
	    }
	    else{
	    	//某个状态
	        $this->db->select('count(cash_record.id) AS total, sum(cash_record.money) as money');
			$this->db->from('cash_record');
			$this->db->join('member', 'member.id = cash_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(
				"cash_record.type" => $status
			);
			$this->db->where($status_where);
			
		    if($userid != 'empty'){
				$user_where = array(
					"cash_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " cash_record.created_at >= '".$begin."' AND cash_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('cash_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				if($status == 4){
					$plusCount = $data['total'];
					$plusMoney = $data['money'];
					$minusCount = 0;
					$minusMoney = 0;
				}
				else{
					$minusCount = $data['total'];
				    $minusMoney = $data['money'];
				    $plusCount = 0;
					$plusMoney = 0;
				}
			}
			else{
				$plusCount = 0;
				$plusMoney = 0;
				$minusCount = 0;
				$minusMoney = 0;
			}
	    }
		
		//分页
		$this->db->select('cash_record.*, member.name, member.mobile');
		$this->db->from('cash_record');
		$this->db->join('member', 'member.id = cash_record.user_id');
		
		$this->db->where($where);
	    if($status != 99){
			$status_where = array(
				"cash_record.type" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"cash_record.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " cash_record.created_at >= '".$begin."' AND cash_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('cash_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('cash_record.id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $plusCount + $minusCount,
			    "plusMoney" => round($plusMoney, 2),
			    "minusMoney" => round($minusMoney, 2),
		        "data" => $return
		    );
		}
		else{
			return null;
		}
	}
	
	public function deleteCashBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('cash_record', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getCommisionPageData($userid, $start, $end, $searchkey, $offset, $num, $type){
	    $where = 'commision_record.deleted_at is null';

		//总数
		$this->db->select('count(commision_record.id) AS total, sum(commision_record.commision) as commision, sum(commision_record.order_money) as order_money, sum(commision_record.pay_money) as pay_money');
		$this->db->from('commision_record');
		$this->db->join('member AS owner', 'owner.id = commision_record.owner_user_id');
		$this->db->join('member AS recharge', 'recharge.id = commision_record.recharge_user_id');
		
		$this->db->where($where);
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " commision_record.created_at >= '".$begin."' AND commision_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"commision_record.owner_user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($type != 99){
			$type_where = array(
				"commision_record.type" => $type
			);
			$this->db->where($type_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('commision_record.order_id', $searchkey);
			$this->db->or_like('owner.name', $searchkey);
			$this->db->or_like('recharge.name', $searchkey);
			$this->db->or_like('owner.mobile', $searchkey);
			$this->db->or_like('recharge.mobile', $searchkey);
			$this->db->group_end();
		}
		
		
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['total'];
			$commision = $data['commision'];
			$order_money = $data['order_money'];
			$pay_money = $data['pay_money'];
		}
		else{
			$count = 0;
			$commision = 0;
			$order_money = 0;
			$pay_money = 0;
		}
		
		//分页
		$this->db->select('commision_record.*, owner.name AS owner_name, owner.mobile AS owner_mobile, recharge.name AS recharge_name, recharge.mobile AS recharge_mobile');
		$this->db->from('commision_record');
		$this->db->join('member AS owner', 'owner.id = commision_record.owner_user_id');
		$this->db->join('member AS recharge', 'recharge.id = commision_record.recharge_user_id');
		
		$this->db->where($where);
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " commision_record.created_at >= '".$begin."' AND commision_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"commision_record.owner_user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($type != 99){
			$type_where = array(
				"commision_record.type" => $type
			);
			$this->db->where($type_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('commision_record.order_id', $searchkey);
			$this->db->or_like('owner.name', $searchkey);
			$this->db->or_like('recharge.name', $searchkey);
			$this->db->or_like('owner.mobile', $searchkey);
			$this->db->or_like('recharge.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('commision_record.id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $count,
			    "commision" => round($commision, 2),
			    "order_money" => round($order_money, 2),
			    "pay_money" => round($pay_money, 2),
		        "data" => $return
		    );
		}
		else{
			return null;
		}
	}
	
	public function deleteCommisionBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('commision_record', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getExchangePageData($userid, $status, $start, $end, $searchkey, $offset, $num){
	    $where = 'exchange_record.deleted_at is null';

		//总数
	    if($status == 99){
	    	//正值
			$this->db->select('count(exchange_record.id) AS total, sum(exchange_record.point) as point');
			$this->db->from('exchange_record');
			$this->db->join('member', 'member.id = exchange_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(1, 3, 4);
			$this->db->where_in('exchange_record.type', $status_where);
			
			if($userid != 'empty'){
				$user_where = array(
					"exchange_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " exchange_record.created_at >= '".$begin."' AND exchange_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('exchange_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				$plusCount = $data['total'];
				$plusPoint = $data['point'] ? $data['point'] : 0;
			}
			else{
				$plusCount = 0;
				$plusPoint = 0;
			}
			
	        //负值
			$this->db->select('count(exchange_record.id) AS total, sum(exchange_record.point) as point');
			$this->db->from('exchange_record');
			$this->db->join('member', 'member.id = exchange_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(
				"exchange_record.type" => 2
			);
			$this->db->where($status_where);
			
			if($userid != 'empty'){
				$user_where = array(
					"exchange_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " exchange_record.created_at >= '".$begin."' AND exchange_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('exchange_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				$minusCount = $data['total'];
				$minusPoint = $data['point'] ? $data['point'] : 0;
			}
			else{
				$minusCount = 0;
				$minusPoint = 0;
			}
	    }
	    else{
	        $this->db->select('count(exchange_record.id) AS total, sum(exchange_record.point) as point');
			$this->db->from('exchange_record');
			$this->db->join('member', 'member.id = exchange_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(
				"exchange_record.type" => $status
			);
			$this->db->where($status_where);
			
			if($userid != 'empty'){
				$user_where = array(
					"exchange_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " exchange_record.created_at >= '".$begin."' AND exchange_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('exchange_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				if($status == 2){
					$minusCount = $data['total'];
					$minusPoint = $data['point'] ? $data['point'] : 0;
					$plusCount = 0;
				    $plusPoint = 0;
				}
				else{
					$minusCount = 0;
					$minusPoint = 0;
					$plusCount = $data['total'];
				    $plusPoint = $data['point'] ? $data['point'] : 0;
				}
			}
			else{
				$minusCount = 0;
				$minusPoint = 0;
				$plusCount = 0;
				$plusPoint = 0;
			}
	    }
		
		//分页
		$this->db->select('exchange_record.*, member.name, member.mobile');
		$this->db->from('exchange_record');
		$this->db->join('member', 'member.id = exchange_record.user_id');
		
		$this->db->where($where);
	    if($status != 99){
			$status_where = array(
				"exchange_record.type" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"exchange_record.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " exchange_record.created_at >= '".$begin."' AND exchange_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('exchange_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('exchange_record.id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $minusCount + $plusCount,
			    "minusPoint" => $minusPoint,
			    "plusPoint" => $plusPoint,
		        "data" => $return
		    );
		}
		else{
			return null;
		}
	}
	
	public function deleteExchangeBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('exchange_record', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getConsumePageData($userid, $status, $start, $end, $searchkey, $offset, $num){
	    $where = 'consume_record.deleted_at is null';

		//总数
		$this->db->select('count(consume_record.id) AS total, sum(consume_record.point) as point');
		$this->db->from('consume_record');
		$this->db->join('member', 'member.id = consume_record.user_id');
		
		$this->db->where($where);
		
		if($status != 99){
			$status_where = array(
				"consume_record.type" => $status
			);
			$this->db->where($status_where);
		}
		
	    $reason_where = array(
			"consume_record.reason" => 2
		);
		$this->db->where($reason_where);
		
	    if($userid != 'empty'){
			$user_where = array(
				"consume_record.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
		    $over = $end.' 23:59:59';
	    	$time_where = " consume_record.created_at >= '".$begin."' AND consume_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('consume_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		
		
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$plusCount = $data['total'];
			$plusPoint = $data['point'] ? $data['point'] : 0;
		}
		else{
			$plusCount = 0;
			$plusPoint = 0;
		}
		
		//负数
        $this->db->select('count(consume_record.id) AS total, sum(consume_record.point) as point');
		$this->db->from('consume_record');
		$this->db->join('member', 'member.id = consume_record.user_id');
		
		$this->db->where($where);
	    if($status != 99){
			$status_where = array(
				"consume_record.type" => $status
			);
			$this->db->where($status_where);
		}
		
	    $reason_where = array(
			"consume_record.reason" => 1
		);
		$this->db->where($reason_where);
		
	    if($userid != 'empty'){
			$user_where = array(
				"consume_record.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
		    $over = $end.' 23:59:59';
	    	$time_where = " consume_record.created_at >= '".$begin."' AND consume_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('consume_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		
		
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$minusCount = $data['total'];
			$minusPoint = $data['point'] ? $data['point'] : 0;
		}
		else{
			$minusCount = 0;
			$minusPoint = 0;
		}
	    
		
		//分页
		$this->db->select('consume_record.*, member.name, member.mobile');
		$this->db->from('consume_record');
		$this->db->join('member', 'member.id = consume_record.user_id');
		
		$this->db->where($where);
	    if($status != 99){
			$status_where = array(
				"consume_record.type" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"consume_record.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
		    $over = $end.' 23:59:59';
	    	$time_where = " consume_record.created_at >= '".$begin."' AND consume_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('consume_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('consume_record.id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $plusCount + $minusCount,
			    "plusPoint" => $plusPoint,
			    "minusPoint" => $minusPoint,
		        "data" => $return
		    );
		}
		else{
			return null;
		}
	}
	
	public function deleteConsumeBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('consume_record', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getUseablePageData($userid, $status, $start, $end, $searchkey, $offset, $num){
	    $where = 'useable_record.deleted_at is null';

		//总数
	    if($status == 99){
	    	//正数
			$this->db->select('count(useable_record.id) AS total, sum(useable_record.point) as point');
			$this->db->from('useable_record');
			$this->db->join('member', 'member.id = useable_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(3, 4);
			$this->db->where_in('useable_record.type', $status_where);
			
		    if($userid != 'empty'){
				$user_where = array(
					"useable_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " useable_record.created_at >= '".$begin."' AND useable_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('useable_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				$plusCount = $data['total'];
				$plusPoint = $data['point'] ? $data['point'] : 0;
			}
			else{
				$plusCount = 0;
				$plusPoint = 0;
			}
			
			//负数
	        $this->db->select('count(useable_record.id) AS total, sum(useable_record.point) as point');
			$this->db->from('useable_record');
			$this->db->join('member', 'member.id = useable_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(1, 2);
			$this->db->where_in('useable_record.type', $status_where);
			
		    if($userid != 'empty'){
				$user_where = array(
					"useable_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " useable_record.created_at >= '".$begin."' AND useable_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('useable_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				$minusCount = $data['total'];
				$minusPoint = $data['point'] ? $data['point'] : 0;
			}
			else{
				$minusCount = 0;
				$minusPoint = 0;
			}
	    }
	    else{
	        $this->db->select('count(useable_record.id) AS total, sum(useable_record.point) as point');
			$this->db->from('useable_record');
			$this->db->join('member', 'member.id = useable_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(
				"useable_record.type" => $status
			);
			$this->db->where($status_where);
			
		    if($userid != 'empty'){
				$user_where = array(
					"useable_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " useable_record.created_at >= '".$begin."' AND useable_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('useable_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				if($status == 3 || $status == 4){
				    $plusCount = $data['total'];
				    $plusPoint = $data['point'] ? $data['point'] : 0;
				    $minusCount = 0;
				    $minusPoint = 0;
				}
				else{
					$plusCount = 0;
				    $plusPoint = 0;
					$minusCount = $data['total'];
				    $minusPoint = $data['point'] ? $data['point'] : 0;
				}
			}
			else{
				$plusCount = 0;
				$plusPoint = 0;
				$minusCount = 0;
				$minusPoint = 0;
			}
	    }
		
		//分页
		$this->db->select('useable_record.*, member.name, member.mobile');
		$this->db->from('useable_record');
		$this->db->join('member', 'member.id = useable_record.user_id');
		
		$this->db->where($where);
	    if($status != 99){
			$status_where = array(
				"useable_record.type" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"useable_record.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " useable_record.created_at >= '".$begin."' AND useable_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('useable_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('useable_record.id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $plusCount + $minusCount,
			    "plusPoint" => $plusPoint,
			    "minusPoint" => $minusPoint,
		        "data" => $return
		    );
		}
		else{
			return null;
		}
	}
	
	public function deleteUseableBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('useable_record', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getSharePageData($userid, $status, $start, $end, $searchkey, $offset, $num){
	    $where = 'share_record.deleted_at is null';

		//总数
	    if($status == 99){
	    	//正数
			$this->db->select('count(share_record.id) AS total, sum(share_record.point) as point');
			$this->db->from('share_record');
			$this->db->join('member', 'member.id = share_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(1, 3);
			$this->db->where_in('share_record.type', $status_where);
			
		    if($userid != 'empty'){
				$user_where = array(
					"share_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " share_record.created_at >= '".$begin."' AND share_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('share_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				$plusCount = $data['total'];
				$plusPoint = $data['point'];
			}
			else{
				$plusCount = 0;
				$plusPoint = 0;
			}
			
			//负数
	        $this->db->select('count(share_record.id) AS total, sum(share_record.point) as point');
			$this->db->from('share_record');
			$this->db->join('member', 'member.id = share_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(
				"share_record.type" => 2
			);
			$this->db->where($status_where);
			
		    if($userid != 'empty'){
				$user_where = array(
					"share_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " share_record.created_at >= '".$begin."' AND share_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('share_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				$minusCount = $data['total'];
				$minusPoint = $data['point'];
			}
			else{
				$minusCount = 0;
				$minusPoint = 0;
			}
	    }
	    else{
	        $this->db->select('count(share_record.id) AS total, sum(share_record.point) as point');
			$this->db->from('share_record');
			$this->db->join('member', 'member.id = share_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(
				"share_record.type" => $status
			);
			$this->db->where($status_where);
			
		    if($userid != 'empty'){
				$user_where = array(
					"share_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " share_record.created_at >= '".$begin."' AND share_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('share_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				if($status == 2){
				    $minusCount = $data['total'];
				    $minusPoint = $data['point'];
				    $plusCount = 0;
				    $plusPoint = 0;
				}
				else{
					$plusCount = $data['total'];
					$plusPoint = $data['point'];
					$minusCount = 0;
				    $minusPoint = 0;
				}
			}
			else{
				$minusCount = 0;
				$minusPoint = 0;
				$plusCount = 0;
				$plusPoint = 0;
			}
	    }
		
		//分页
		$this->db->select('share_record.*, member.name, member.mobile');
		$this->db->from('share_record');
		$this->db->join('member', 'member.id = share_record.user_id');
		
		$this->db->where($where);
	    if($status != 99){
			$status_where = array(
				"share_record.type" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"share_record.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " share_record.created_at >= '".$begin."' AND share_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('share_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('share_record.id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $minusCount + $plusCount,
			    "plusPoint" => $plusPoint,
			    "minusPoint" => $minusPoint,
		        "data" => $return
		    );
		}
		else{
			return null;
		}
	}
	
	public function deleteShareBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('share_record', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getWaitPageData($userid, $status, $start, $end, $searchkey, $offset, $num){
	    $where = 'shoot_record.deleted_at is null';

		//总数
		$this->db->select('count(shoot_record.id) AS total, sum(shoot_record.shoot_point) as shoot_point, sum(shoot_record.future_point) as future_point');
		$this->db->from('shoot_record');
		$this->db->join('member', 'member.id = shoot_record.user_id');
		
		$this->db->where($where);
	    if($status != 99){
			$status_where = array(
				"shoot_record.type" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"shoot_record.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " shoot_record.created_at >= '".$begin."' AND shoot_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('shoot_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		
		
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['total'];
			$shoot_point = $data['shoot_point'];
			$future_point = $data['future_point'];
		}
		else{
			$count = 0;
			$shoot_point = 0;
			$future_point = 0;
		}
		
		//分页
		$this->db->select('shoot_record.*, member.name, member.mobile');
		$this->db->from('shoot_record');
		$this->db->join('member', 'member.id = shoot_record.user_id');
		
		$this->db->where($where);
	    if($status != 99){
			$status_where = array(
				"shoot_record.type" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"shoot_record.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " shoot_record.created_at >= '".$begin."' AND shoot_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('shoot_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('shoot_record.id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $count,
			    "shoot_point" => $shoot_point,
			    "future_point" => $future_point,
		        "data" => $return
		    );
		}
		else{
			return null;
		}
	}
	
	public function deleteWaitBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('shoot_record', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
    public function allOrderStatusData() {
        $where = 'deleted_at is null';
		$this->db->select('status, created_at');
		$this->db->where($where);
	
		$this->db->order_by('id', 'DESC');
		
		$query = $this->db->get('orders');
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
	public function welcomeOrderData() {
		$start = date('Y-m-d', time()-30*24*60*60).' 00:00:00';
	    $end = date('Y-m-d', time()-24*60*60).' 23:59:59';
		$sql = "SELECT * FROM cash_record WHERE created_at >= '".$start."' AND created_at <= '".$end."' AND type = 1 AND deleted_at is null ORDER BY id DESC";
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function welcomeChargeData() {
		$start = date('Y-m-d', time()-30*24*60*60).' 00:00:00';
	    $end = date('Y-m-d', time()-24*60*60).' 23:59:59';
		$sql = "SELECT * FROM recharge_record WHERE created_at >= '".$start."' AND created_at <= '".$end."' AND deleted_at is null ORDER BY id DESC";
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
    public function allCashData() {
		$sql = "SELECT SUM(money) AS money FROM cash_record WHERE deleted_at is null AND type = 1";
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->row_array();
			return round($return['money'], 2);
		}
		else{
			return 0;
		}
	}
	
    public function getWaitSendData() {
        $where = 'deleted_at is null AND status = 1';
		$this->db->select('count(id) AS total');
		$this->db->where($where);
			
		$query = $this->db->get('orders');
	    if ($query->num_rows() > 0){
	    	$data = $query->row_array();
			return $data['total'];
		}
		else{
			return 0;
		}
	}
	
    public function getWaitRefundData() {
        $where = 'deleted_at is null AND status = 7';
		$this->db->select('count(id) AS total');
		$this->db->where($where);
			
		$query = $this->db->get('orders');
	    if ($query->num_rows() > 0){
	    	$data = $query->row_array();
			return $data['total'];
		}
		else{
			return 0;
		}
	}
	
	public function getUpgradePageData($time, $searchkey, $offset, $num){
	    $where = 'upgrade_record.deleted_at is null';

		//总数
		$this->db->select('count(upgrade_record.id) AS total');
		$this->db->from('upgrade_record');
		$this->db->join('member', 'member.id = upgrade_record.user_id');
		
		$this->db->where($where);
		
	    if($time){
	    	$begin = $time.' 00:00:00';
	    	$end = $time.' 23:59:59';
	    	$time_where = " upgrade_record.created_at >= '".$begin."' AND upgrade_record.created_at <= '".$end."'";
			$this->db->where($time_where);
		}
		
	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('upgrade_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
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
		$this->db->select('upgrade_record.*, member.name, member.mobile');
		$this->db->from('upgrade_record');
		$this->db->join('member', 'member.id = upgrade_record.user_id');
		
		$this->db->where($where);
		
	    if($time){
	    	$begin = $time.' 00:00:00';
	    	$end = $time.' 23:59:59';
	    	$time_where = " upgrade_record.created_at >= '".$begin."' AND upgrade_record.created_at <= '".$end."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('upgrade_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('upgrade_record.id', 'DESC');
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
	
	public function deleteUpgradeBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('upgrade_record', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function quickDeclaration($mobile, $money, $schedule, $router, $recharge_user_id, $order_id, $commisionSetting) {
		$this->load->model('member_model');
		$transaction_id = 'backend_quick_process';
		$pay_time = date('Y-m-d H:i:s', time());
		$service_user_id = 1;
		
		$total = $money;
		$point = 0;
		$recharge_user_id = $recharge_user_id;
		$exchange_point = $total * $schedule['give_exchange_rate'] * 0.01;
		$future_point = $total * $schedule['wait_rate'];
		$consume_point = $total * $schedule['give_consume_rate'] * 0.01;
		$share_point = $total * $schedule['give_share_rate'] * 0.01;
		$active_timestamp = time() + $schedule['wait_period'] * 24 * 60 * 60;
		$active_time = date('Y-m-d H:i:s', $active_timestamp);
		$pay_money = $money;
		
		/*复投设置begin*/
		$setting_res = false;
    	if($schedule['charge_repeat_rate'] && strpos($schedule['charge_repeat_rate'], '/') === false && $schedule['charge_repeat_period'] && strpos($schedule['charge_repeat_period'], '/') === false){
    		//只有一轮
    		$setting_res = true;
    		$charge_repeat_rate_arr = array($schedule['charge_repeat_rate']);
    		$charge_repeat_peroid_arr = array($schedule['charge_repeat_period']);
    	}
        elseif(strpos($schedule['charge_repeat_rate'], '/') !== false && strpos($schedule['charge_repeat_period'], '/') !== false){
        	//同为多轮
        	$charge_repeat_rate_arr = explode('/', $schedule['charge_repeat_rate']);
        	$charge_repeat_peroid_arr = explode('/', $schedule['charge_repeat_period']);
        	if(count($charge_repeat_rate_arr) == count($charge_repeat_peroid_arr)){
        		//轮数设置相同
    		    $setting_res = true;
        	}
    	}
    	
    	if(!$setting_res){
    		return false;
    	}
    	else{
    		$repeatParam = array();
    		for($i=0; $i<count($charge_repeat_rate_arr); $i++){
    			if($i == 0){
    				$begin_time = $active_timestamp + 1;
    				$end_time = $begin_time + $charge_repeat_peroid_arr[$i] * 24 * 60 * 60;
    				$limit_point = $future_point;
    			}
    			else{
    				$begin_time = $end_time + 1;
    				$end_time = $begin_time + $charge_repeat_peroid_arr[$i] * 24 * 60 * 60;
    				$limit_point = $limit_point * $charge_repeat_rate_arr[$i-1];
    			}
    			
	    		$repeatParam[] = array(
	    		    "order_id" => $order_id,
					"user_id" => $recharge_user_id,
	    		    "rate" => $charge_repeat_rate_arr[$i],
	    		    "day" => $charge_repeat_peroid_arr[$i],
				    "already_shot_point" => 0,
	    		    "type" => 1, //1报单 2升级 3商家 4补偿
				    "limit_point" => $limit_point,
		    		"begin_time" => date('Y-m-d H:i:s', $begin_time),
		    		"end_time" => date('Y-m-d H:i:s', $end_time)
				);
    		}
    	}
    	/*复投设置end*/
		
		//报单订单
		$recharge_param = array(
		    "service_user_id" => $service_user_id,
    	    "recharge_user_id" => $recharge_user_id,
		    "recharge_money" => $total,
		    "pay_money" => $pay_money,
	    	"point" => $point,
    	    "order_id" => $order_id,
		    "status" => 1
		);
		
		//生成现金记录
		$paramCash = array(
			"type" => 2,  //1兑换商品 2服务中心充值
		    "money" => $money,
		    "pay_no" => $transaction_id, //转出
	    	"user_id" => $recharge_user_id,
		    "order_id" => $order_id
		);
		
		$service_consume_param = array(
    	    "type" => 7, //1可用积分转消费积分 2积分转让 3充值报单 4兑换商品 5升级服务中心 6分享 7报单抵扣
    	    "reason" => 1,  //1转出  2转入
		    "point" => $point,
	    	"user_id" => $service_user_id,
		    "order_id" => $order_id
		);
		
		$serviceMemberSql = "UPDATE member SET consume_point = consume_point - ".$point." WHERE id = ".$service_user_id;
		$rechargeMemberSql = "UPDATE member SET consume_point = consume_point + ".$consume_point.", exchange_point = exchange_point + ".$exchange_point.", wait_point = wait_point + ".$future_point.", share_point = share_point + ".$share_point." WHERE id = ".$recharge_user_id;

		//购物券记录
		$exchange_param = array(
		    "type" => 1, //1充值  2兑换商品
		    "point" => $exchange_point,
	    	"user_id" => $recharge_user_id,
    	    "order_id" => $order_id
		);
		
		//待用积分记录
		$wait_param = array(
		    "multiple" => $schedule['wait_rate'],
    	    "day" => $schedule['wait_period'],
		    "shoot_point" => $total,
	    	"future_point" => $future_point,
	    	"active_time" => $active_time,
		    "type" => 2,   //2充值
	    	"user_id" => $recharge_user_id,
    	    "order_id" => $order_id
		);
		
		$consume_param = array(
    	    "type" => 3, //1可用积分转消费积分 2积分转让 3充值报单 4兑换商品 5升级服务中心 6分享 7报单抵扣
    	    "reason" => 2,  //1转出  2转入
		    "point" => $consume_point,
	    	"user_id" => $recharge_user_id,
		    "order_id" => $order_id
		);
		
		$share_param = array(
    	    "type" => 1,  //1自己充值增加  2下线充值返利减少  3升级到服务中心
		    "point" => $share_point,
	    	"user_id" => $recharge_user_id,
		    "order_id" => $order_id
		);
		
	    //事务
        $this->db->trans_begin();
	    if($setting_res && isset($repeatParam) && !empty($repeatParam)){
	        $length = count($repeatParam);
        	$lastIndex = $length - 1;
        	for($i=0; $i<$length; $i++){
        		$repeatParam[$i]['round'] = $i + 2;
        		if($i == $lastIndex){
        			$repeatParam[$i]['auto_to_consume'] = 1;
        		}
        		$this->db->insert('repeat_info', $repeatParam[$i]);
        	}
        }
        
        $this->db->insert('recharge_record', $recharge_param);
		$this->db->insert('cash_record', $paramCash);
        $this->db->insert('exchange_record', $exchange_param);
        $this->db->insert('shoot_record', $wait_param);
        $this->db->insert('consume_record', $consume_param);
        $this->db->insert('share_record', $share_param);
        if($point && $point > 0){
            $this->db->query($serviceMemberSql);
            $this->db->insert('consume_record', $service_consume_param);
        }
        $this->db->query($rechargeMemberSql);
        
        //计算上级会员积分和上级服务中心佣金
	    if(strstr($router, ',')){
    	    $router_array = explode(',', $router);
    	    $last = count($router_array) - 1;
    	    unset($router_array[$last]);
    	    $router_array = array_reverse($router_array);
    	    $service_center_record = array();
    	    $junior_first_commision = $pay_money * $commisionSetting['junior_first'] * 0.01;
    	    $junior_second_commision = $pay_money * $commisionSetting['junior_second'] * 0.01;
    	    $junior_third_commision = $pay_money * $commisionSetting['junior_third'] * 0.01;
    	    $middle_first_commision = $pay_money * $commisionSetting['middle_first'] * 0.01;
    	    $middle_second_commision = $pay_money * $commisionSetting['middle_second'] * 0.01;
    	    $middle_third_commision = $pay_money * $commisionSetting['middle_third'] * 0.01;
    	    $advanced_first_commision = $pay_money * $commisionSetting['advanced_first'] * 0.01;
    	    $advanced_second_commision = $pay_money * $commisionSetting['advanced_second'] * 0.01;
    	    $advanced_third_commision = $pay_money * $commisionSetting['advanced_third'] * 0.01;
    	    
    	    /*
    	     * 计算上两层会员返利
    	     */
    	    for($i=0; $i<count($router_array); $i++){
    	    	$user = $this->member_model->getDataById($router_array[$i]);
    	    	if($user !== null){
    	    		$return_share_param = array(
			    	    "type" => 2,  //1自己充值增加  2下线充值返利减少  3升级到服务中心
				    	"user_id" => $user['id'],
					    "order_id" => $order_id
					);
					
					/*
					$return_consume_param = array(
			    	    "type" => 6, //1可用积分转消费积分  2积分转让  3充值报单  4兑换商品   5升级服务中心  6分享
			    	    "reason" => 2,  //1转出  2转入
				    	"user_id" => $user['id'],
					    "order_id" => $order_id
					);
					*/
					
					$return_commision_param = array(
			    	    "level" => $user['level'],
					    "owner_user_id" => $user['id'],
				    	"recharge_user_id" => $recharge_user_id,
        			    "order_money" => $total,
        			    "pay_money" => $pay_money,
					    "order_id" => $order_id,
        			    "type" => 1
					);
					
	    	    	if($i == 0){
	    	    		if($user['level'] >= $schedule['first_level_must'] && $schedule['first_level_rate'] > 0){
		    	    		//上面一层会员（服务中心也是会员）必须满足设置要求
		    	    		$compare_share_point = $total * $schedule['first_level_rate'] * 0.01;
		    	    		if($user['share_point'] > $compare_share_point){
		    	    			$first_get_point = $compare_share_point;
		    	    		}
		    	    		else{
		    	    			$first_get_point = $user['share_point'];
		    	    		}
		    	    		
		    	    		if($first_get_point > 0){
			    	    		$return_share_param['point'] = $first_get_point;
			    	    		$return_commision_param['commision'] = $first_get_point;
								
			    	    		$firstMemberSql = "UPDATE member SET commision = commision + ".$first_get_point.", share_point = share_point - ".$first_get_point." WHERE id = ".$user['id'];
			    	    		$this->db->query($firstMemberSql);
			    	    		
			    	    		//返利记录
			    	    		$this->db->insert('commision_record', $return_commision_param);
		                        $this->db->insert('share_record', $return_share_param);
		    	    		}
	    	    		}
	    	    	}
	    	    	elseif($i == 1){
	    	    		//上面二层会员，必须满足设置要求
	    	    		if($user['level'] >= $schedule['second_level_must'] && $schedule['second_level_rate'] > 0){
		    	    		$compare_share_point = $total * $schedule['second_level_rate'] * 0.01;
		    	    		if($user['share_point'] > $compare_share_point){
		    	    			$second_get_point = $compare_share_point;
		    	    		}
		    	    		else{
		    	    			$second_get_point = $user['share_point'];
		    	    		}
		    	    		
		    	    		$return_share_param['point'] = $second_get_point;
	    	    		    $return_commision_param['commision'] = $second_get_point;
	    	    		
	    	    		    if($second_get_point > 0){
			    	    		$secondMemberSql = "UPDATE member SET commision = commision + ".$second_get_point.", share_point = share_point - ".$second_get_point." WHERE id = ".$user['id'];
			    	    		$this->db->query($secondMemberSql);
			    	    		
			    	    		//返利记录
			    	    		$this->db->insert('commision_record', $return_commision_param);
	                            $this->db->insert('share_record', $return_share_param);
	    	    		    }
	    	    		}
	    	    	}
    	    	    elseif($i == 2){
	    	    		//上面三层会员，必须满足设置要求
	    	    		if($user['level'] >= $schedule['third_level_must'] && $schedule['third_level_rate'] > 0){
		    	    		$compare_share_point = $total * $schedule['third_level_rate'] * 0.01;
		    	    		if($user['share_point'] > $compare_share_point){
		    	    			$third_get_point = $compare_share_point;
		    	    		}
		    	    		else{
		    	    			$third_get_point = $user['share_point'];
		    	    		}
		    	    		
		    	    		$return_share_param['point'] = $third_get_point;
	    	    		    $return_commision_param['commision'] = $third_get_point;
	    	    		
	    	    		    if($third_get_point > 0){
			    	    		$thirdMemberSql = "UPDATE member SET commision = commision + ".$third_get_point.", share_point = share_point - ".$third_get_point." WHERE id = ".$user['id'];
			    	    		$this->db->query($thirdMemberSql);
			    	    		
			    	    		//返利记录
			    	    		$this->db->insert('commision_record', $return_commision_param);
	                            $this->db->insert('share_record', $return_share_param);
	    	    		    }
	    	    		}
	    	    		
	    	    		//满三层跳出循环
	    	    		break;
	    	    	}
    	    	}
    	    }
    	    
    	    /*
    	     *      计算上三层佣金返利
    	     *      服务中心拥挤规则，从下往上算  例以3000计算
    	     * 1.	初级150->中级150-高级150
    	     * 2.	初级150->高级300
    	     * 3.	中级300->高级150
    	     * 4.	高级450
    	     * 5.	钱包150=3000*5%
    	     */
    	    $junior_time = 0;
    	    $middle_time = 0;
    	    $advanced_time = 0;
		    for($i=0; $i<count($router_array); $i++){
	        	$user = $this->member_model->getDataById($router_array[$i]);
	        	if($user !== null){
	        		$typeArray = explode(',', $user['type']);
	        	    //如果是服务中心
	        		if(in_array(2, $typeArray)){
    	    			$commision_param = array(
				    	    "level" => $user['level'],
						    "owner_user_id" => $user['id'],
					    	"recharge_user_id" => $recharge_user_id,
    	    			    "order_money" => $total,
    	    			    "pay_money" => $pay_money,
						    "order_id" => $order_id
						);
						
	        		    if(empty($service_center_record)){
	        		        //还没有分配过佣金
	    	    			if($user['level'] == 11){
	    	    				//初级服务中心
	    	    				$commision = $junior_first_commision;
	    	    				$junior_time++;
	    	    			}
	    	    			elseif($user['level'] == 12){
	    	    				//中级服务中心    跳过初级服务中心   吃掉初级第一层的佣金
	    	    				$commision = $junior_first_commision + $middle_first_commision;
	    	    				$middle_time++;
	    	    			}
	    	    		    elseif($user['level'] == 13){
	    	    				//高级服务中心  跳过初级服务中心和中级服务中心    吃掉初级第一层的佣金和中级第一层的佣金
	    	    				$commision = $junior_first_commision + $middle_first_commision + $advanced_first_commision;
	    	    				$advanced_time++;
	    	    			}
	    	    			else{
	    	    				continue;
	    	    			}
	    	    			
	    	    			$service_center_record[] = array(
	    	    			    'commision'=>$commision,
	    	    			    'level'=>$user['level']
	    	    			);
						}
						else{
							$last = count($service_center_record) - 1;
							if($user['level'] >= $service_center_record[$last]['level']){
								//本次等级大于等于上次等级
								if($user['level'] == 11){
									//初级服务中心    这里不可能出现第一次初级服务中心
									if($junior_time == 1){
										//第二次初级服务中心
										$commision = $junior_second_commision;
										$junior_time++;
									}
									elseif($junior_time == 2){
										//第三次初级服务中心
										$commision = $junior_third_commision;
										$junior_time++;
									}
									else{
										continue;
									}
								}
								elseif($user['level'] == 12){
									//中级服务中心
									if($middle_time == 0){
										$commision = $middle_first_commision;
										$middle_time++;
									}
									elseif($middle_time == 1){
										//第二次中级服务中心
										$commision = $middle_second_commision;
										$middle_time++;
									}
									elseif($middle_time == 2){
										//第三次中级服务中心
										$commision = $middle_third_commision;
										$middle_time++;
									}
									else{
										continue;
									}
								}
								elseif($user['level'] == 13){
									//高级服务中心
									if($advanced_time == 0){
										//还没算过高级
										if($service_center_record[$last]['level'] == 11){
											//上次是初级   跳过中级服务中心   吃掉中级第一层的佣金   第一次高级
				    	    				$commision = $middle_first_commision + $advanced_first_commision;
				    	    				$advanced_time++;
										}
										elseif($service_center_record[$last]['level'] == 12){
											//上次是中级   第一次高级
				    	    				$commision = $advanced_first_commision;
				    	    				$advanced_time++;
										}
									}
									elseif($advanced_time == 1){
										//算过一次高级
										$commision = $advanced_senond_commision;
										$advanced_time++;
									}
								    elseif($advanced_time == 2){
										//算过二次高级
										$commision = $advanced_third_commision;
										$advanced_time++;
										break;
									}
								    else{
										continue;
									}
								}
								else{
									continue;
								}
								
								$service_center_record[] = array(
		    	    			    'commision'=>$commision,
		    	    			    'level'=>$user['level']
		    	    			);
							}
							else{
								continue;
							}
						}
						
	        		    if(!isset($commision) || $commision == 0){
							continue;
						}
    	    			
    	    			$commisionSql = "UPDATE member SET commision = commision + ".$commision." WHERE id = ".$user['id'];
    	    			$commision_param['commision'] = $commision;
    	    			$this->db->insert('commision_record', $commision_param);
    	    			$this->db->query($commisionSql);
    	    		}
	        	}
	    	}
    	}
    	
    	//升级
	    $userRecharge = $this->member_model->getDataById($recharge_user_id);
		if($userRecharge !== null){
			//自身升级
			if($userRecharge['level'] == 0 && $money >= 1000){
				//被报单1000后    更新自己等级
				$selfSql = "UPDATE member SET level = 1 WHERE id = ".$recharge_user_id;
				$this->db->query($selfSql);
				
				//升级记录
				$self_upgrade_param = array(
		    	    "old_level" => 0,
				    "new_level" => 1,
			    	"user_id" => $recharge_user_id,
				    "order_id" => $order_id
				);
				$this->db->insert('upgrade_record', $self_upgrade_param);
				
				
				//自身升级到VIP才有可能计算上级是否符合升级     判断上级是否满足5个VIP  已经是VIP1则不更新
				$parent = $this->member_model->getDataById($userRecharge['parent_id']);
				if($parent !== null){
					$childVipNum = $this->member_model->getChildrenVipNum($userRecharge['parent_id']);
					if($parent['level'] == 1 && $childVipNum >= 5){
						//更新推荐人等级
						$recommendSql = "UPDATE member SET level = 2 WHERE id = ".$userRecharge['parent_id'];
						$this->db->query($recommendSql);
						
						//升级记录
						$parent_upgrade_param = array(
				    	    "old_level" => 1,
						    "new_level" => 2,
					    	"user_id" => $userRecharge['parent_id'],
						    "order_id" => $order_id
						);
						$this->db->insert('upgrade_record', $parent_upgrade_param);
					}
				}
			}
		}
        
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
	
	public function manager_status($id, $value) {
	    $data = array(
	        "is_manager" => $value,
			'updated_at' => date('Y-m-d H:i:s', time()),
		);
		
		$this->db->where('id', $id);
		$this->db->update('member', $data);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function company_status($id, $value) {
	    $data = array(
	        "is_company" => $value,
			'updated_at' => date('Y-m-d H:i:s', time()),
		);
		
		$this->db->where('id', $id);
		$this->db->update('member', $data);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getSshareholderList(){
	    $where = array(
			"is_manager" => 1
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('member');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function getUpgradeDeclarationBackend($userid, $start, $end) {
		$where = 'deleted_at is null';
	    $this->db->select_sum('money');
		$this->db->from('cash_record');
		
		$this->db->where($where);
	    
	    $user_where = array(
			"user_id" => $userid
		);
		$this->db->where($user_where);
		$this->db->where('pay_no', 'pay_under_line');  //后台升级
		
    	$begin = $start.' 00:00:00';
    	$over = $end.' 23:59:59';
    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$over."'";
		$this->db->where($time_where);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return['money'];
		}
		else{
			return 0;
		}
	}
	
	public function getUpgradeDeclarationQuick($userid, $start, $end) {
		$where = 'deleted_at is null';
	    $this->db->select_sum('money');
		$this->db->from('cash_record');
		
		$this->db->where($where);
	    
	    $user_where = array(
			"user_id" => $userid
		);
		$this->db->where($user_where);
		$this->db->where('pay_no', 'backend_quick_process');  //快速报单
		
    	$begin = $start.' 00:00:00';
    	$over = $end.' 23:59:59';
    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$over."'";
		$this->db->where($time_where);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return['money'];
		}
		else{
			return 0;
		}
	}
	
	/*
	 * 作为服务中心给他他人报单
	 */
	public function getOnlineDeclarationData($userid, $start, $end) {
		$where = 'deleted_at is null';
	    $this->db->select_sum('pay_money');
		$this->db->from('recharge_record');
		
		$this->db->where($where);
	    
	    $user_where = array(
			"service_user_id" => $userid
		);
		$this->db->where($user_where);
		
		$status_where = array(
			"status" => 1
		);
		$this->db->where($status_where);

    	$begin = $start.' 00:00:00';
    	$over = $end.' 23:59:59';
    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$over."'";
		$this->db->where($time_where);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return['pay_money'];
		}
		else{
			return 0;
		}
	}
	
	public function serviceDeclaration($point, $total, $service_user_id, $recharge_user, $order_id, $schedule, $commisionSetting){
		$recharge_user_id = $recharge_user['id'];
		$router = $recharge_user['router'];
		$pay_money = $total - $point;
		$exchange_point = $total * $schedule['give_exchange_rate'] * 0.01;
		$future_point = $total * $schedule['wait_rate'];
		$consume_point = $total * $schedule['give_consume_rate'] * 0.01;
		$share_point = $total * $schedule['give_share_rate'] * 0.01;
		$active_timestamp = time() + $schedule['wait_period'] * 24 * 60 * 60;
		$active_time = date('Y-m-d H:i:s', $active_timestamp);
		
	    /*复投设置begin*/
		$setting_res = false;
    	if($schedule['charge_repeat_rate'] && strpos($schedule['charge_repeat_rate'], '/') === false && $schedule['charge_repeat_period'] && strpos($schedule['charge_repeat_period'], '/') === false){
    		//只有一轮
    		$setting_res = true;
    		$charge_repeat_rate_arr = array($schedule['charge_repeat_rate']);
    		$charge_repeat_peroid_arr = array($schedule['charge_repeat_period']);
    	}
        elseif(strpos($schedule['charge_repeat_rate'], '/') !== false && strpos($schedule['charge_repeat_period'], '/') !== false){
        	//同为多轮
        	$charge_repeat_rate_arr = explode('/', $schedule['charge_repeat_rate']);
        	$charge_repeat_peroid_arr = explode('/', $schedule['charge_repeat_period']);
        	if(count($charge_repeat_rate_arr) == count($charge_repeat_peroid_arr)){
        		//轮数设置相同
    		    $setting_res = true;
        	}
    	}
    	
    	if(!$setting_res){
    		return false;
    	}
    	else{
    		$repeatParam = array();
    		for($i=0; $i<count($charge_repeat_rate_arr); $i++){
    			if($i == 0){
    				$begin_time = $active_timestamp + 1;
    				$end_time = $begin_time + $charge_repeat_peroid_arr[$i] * 24 * 60 * 60;
    				$limit_point = $future_point;
    			}
    			else{
    				$begin_time = $end_time + 1;
    				$end_time = $begin_time + $charge_repeat_peroid_arr[$i] * 24 * 60 * 60;
    				$limit_point = $limit_point * $charge_repeat_rate_arr[$i-1];
    			}
    			
	    		$repeatParam[] = array(
	    		    "order_id" => $order_id,
					"user_id" => $recharge_user_id,
	    		    "rate" => $charge_repeat_rate_arr[$i],
	    		    "day" => $charge_repeat_peroid_arr[$i],
				    "already_shot_point" => 0,
	    		    "type" => 1, //1报单 2升级 3商家 4补偿
				    "limit_point" => $limit_point,
		    		"begin_time" => date('Y-m-d H:i:s', $begin_time),
		    		"end_time" => date('Y-m-d H:i:s', $end_time)
				);
    		}
    	}
    	/*复投设置end*/
		
		$recharge_param = array(
		    "service_user_id" => $service_user_id,
    	    "recharge_user_id" => $recharge_user_id,
		    "recharge_money" => $total,
		    "pay_money" => $pay_money,
	    	"point" => $point,
    	    "order_id" => $order_id,
		    "status" => 1
		);
				
		//生成现金记录
		$paramCash = array(
			"type" => 2,  //1兑换商品 2服务中心充值
		    "money" => $pay_money,
		    "pay_no" => 'service_backend_process',
	    	"user_id" => $service_user_id,
		    "order_id" => $order_id
		);
		
		$service_consume_param = array(
    	    "type" => 7, //1可用积分转消费积分 2积分转让 3充值报单 4兑换商品 5升级服务中心 6分享 7报单抵扣
    	    "reason" => 1,  //1转出  2转入
		    "point" => $point,
	    	"user_id" => $service_user_id,
		    "order_id" => $order_id
		);
		
		$serviceMemberSql = "UPDATE member SET consume_point = consume_point - ".$point." WHERE id = ".$service_user_id;
		$rechargeMemberSql = "UPDATE member SET consume_point = consume_point + ".$consume_point.", exchange_point = exchange_point + ".$exchange_point.", wait_point = wait_point + ".$future_point.", share_point = share_point + ".$share_point." WHERE id = ".$recharge_user_id;

		//购物券记录
		$exchange_param = array(
		    "type" => 1, //1充值  2兑换商品
		    "point" => $exchange_point,
	    	"user_id" => $recharge_user_id,
    	    "order_id" => $order_id
		);
		
		//待用积分记录
		$wait_param = array(
		    "multiple" => $schedule['wait_rate'],
    	    "day" => $schedule['wait_period'],
		    "shoot_point" => $total,
	    	"future_point" => $future_point,
	    	"active_time" => $active_time,
		    "type" => 2,   //2充值
	    	"user_id" => $recharge_user_id,
    	    "order_id" => $order_id
		);
		
		$consume_param = array(
    	    "type" => 3, //1可用积分转消费积分 2积分转让 3充值报单 4兑换商品 5升级服务中心 6分享 7报单抵扣
    	    "reason" => 2,  //1转出  2转入
		    "point" => $consume_point,
	    	"user_id" => $recharge_user_id,
		    "order_id" => $order_id
		);
		
		$share_param = array(
    	    "type" => 1,  //1自己充值增加  2下线充值返利减少  3升级到服务中心
		    "point" => $share_point,
	    	"user_id" => $recharge_user_id,
		    "order_id" => $order_id
		);
		
	    //事务
        $this->db->trans_begin();
	    if($setting_res && isset($repeatParam) && !empty($repeatParam)){
	        $length = count($repeatParam);
        	$lastIndex = $length - 1;
        	for($i=0; $i<$length; $i++){
        		$repeatParam[$i]['round'] = $i + 2;
        		if($i == $lastIndex){
        			$repeatParam[$i]['auto_to_consume'] = 1;
        		}
        		$this->db->insert('repeat_info', $repeatParam[$i]);
        	}
        }
        
        $this->db->insert('recharge_record', $recharge_param);
		$this->db->insert('cash_record', $paramCash);
        $this->db->insert('exchange_record', $exchange_param);
        $this->db->insert('shoot_record', $wait_param);
        $this->db->insert('consume_record', $consume_param);
        $this->db->insert('share_record', $share_param);
        if($point && $point > 0){
            $this->db->query($serviceMemberSql);
            $this->db->insert('consume_record', $service_consume_param);
        }
        $this->db->query($rechargeMemberSql);
        
        //计算上级会员积分和上级服务中心佣金
	    if(strstr($router, ',')){
	    	$this->load->model('member_model');
    	    $router_array = explode(',', $router);
    	    $last = count($router_array) - 1;
    	    unset($router_array[$last]);
    	    $router_array = array_reverse($router_array);
    	    $service_center_record = array();
    	    $junior_first_commision = $pay_money * $commisionSetting['junior_first'] * 0.01;
    	    $junior_second_commision = $pay_money * $commisionSetting['junior_second'] * 0.01;
    	    $junior_third_commision = $pay_money * $commisionSetting['junior_third'] * 0.01;
    	    $middle_first_commision = $pay_money * $commisionSetting['middle_first'] * 0.01;
    	    $middle_second_commision = $pay_money * $commisionSetting['middle_second'] * 0.01;
    	    $middle_third_commision = $pay_money * $commisionSetting['middle_third'] * 0.01;
    	    $advanced_first_commision = $pay_money * $commisionSetting['advanced_first'] * 0.01;
    	    $advanced_second_commision = $pay_money * $commisionSetting['advanced_second'] * 0.01;
    	    $advanced_third_commision = $pay_money * $commisionSetting['advanced_third'] * 0.01;
    	    
    	    /*
    	     * 计算上两层会员返利
    	     */
    	    for($i=0; $i<count($router_array); $i++){
    	    	$user = $this->member_model->getDataById($router_array[$i]);
    	    	if($user !== null){
    	    		$return_share_param = array(
			    	    "type" => 2,  //1自己充值增加  2下线充值返利减少  3升级到服务中心
				    	"user_id" => $user['id'],
					    "order_id" => $order_id
					);
					
					/*
					$return_consume_param = array(
			    	    "type" => 6, //1可用积分转消费积分  2积分转让  3充值报单  4兑换商品   5升级服务中心  6分享
			    	    "reason" => 2,  //1转出  2转入
				    	"user_id" => $user['id'],
					    "order_id" => $order_id
					);
					*/
					
					$return_commision_param = array(
			    	    "level" => $user['level'],
					    "owner_user_id" => $user['id'],
				    	"recharge_user_id" => $recharge_user_id,
        			    "order_money" => $total,
        			    "pay_money" => $pay_money,
					    "order_id" => $order_id,
        			    "type" => 1
					);
					
	    	    	if($i == 0){
	    	    		//上面一层会员，必须满足设置要求（服务中心也是会员）
	    	    		if($user['level'] >= $schedule['first_level_must'] && $schedule['first_level_rate'] > 0){
		    	    		$compare_share_point = $total * $schedule['first_level_rate'] * 0.01;
		    	    		if($user['share_point'] > $compare_share_point){
		    	    			$first_get_point = $compare_share_point;
		    	    		}
		    	    		else{
		    	    			$first_get_point = $user['share_point'];
		    	    		}
		    	    		
		    	    		if($first_get_point > 0){
			    	    		$return_share_param['point'] = $first_get_point;
			    	    		$return_commision_param['commision'] = $first_get_point;
								
			    	    		$firstMemberSql = "UPDATE member SET commision = commision + ".$first_get_point.", share_point = share_point - ".$first_get_point." WHERE id = ".$user['id'];
			    	    		$this->db->query($firstMemberSql);
			    	    		
			    	    		//返利记录
			    	    		$this->db->insert('commision_record', $return_commision_param);
		                        $this->db->insert('share_record', $return_share_param);
		    	    		}
	    	    		}
	    	    	}
	    	    	elseif($i == 1){
	    	    		//上面二层会员，必须满足设置要求
	    	    		if($user['level'] >= $schedule['second_level_must'] && $schedule['second_level_rate'] > 0){
		    	    		$compare_share_point = $total * $schedule['second_level_rate'] * 0.01;
		    	    		if($user['share_point'] > $compare_share_point){
		    	    			$second_get_point = $compare_share_point;
		    	    		}
		    	    		else{
		    	    			$second_get_point = $user['share_point'];
		    	    		}
		    	    		
		    	    		$return_share_param['point'] = $second_get_point;
	    	    		    $return_commision_param['commision'] = $second_get_point;
	    	    		
	    	    		    if($second_get_point > 0){
			    	    		$secondMemberSql = "UPDATE member SET commision = commision + ".$second_get_point.", share_point = share_point - ".$second_get_point." WHERE id = ".$user['id'];
			    	    		$this->db->query($secondMemberSql);
			    	    		
			    	    		//返利记录
			    	    		$this->db->insert('commision_record', $return_commision_param);
	                            $this->db->insert('share_record', $return_share_param);
	    	    		    }
	    	    		}
	    	    	}
    	    	    elseif($i == 2){
	    	    		//上面三层会员，必须满足设置要求
	    	    		if($user['level'] >= $schedule['third_level_must'] && $schedule['third_level_rate'] > 0){
		    	    		$compare_share_point = $total * $schedule['third_level_rate'] * 0.01;
		    	    		if($user['share_point'] > $compare_share_point){
		    	    			$third_get_point = $compare_share_point;
		    	    		}
		    	    		else{
		    	    			$third_get_point = $user['share_point'];
		    	    		}
		    	    		
		    	    		$return_share_param['point'] = $third_get_point;
	    	    		    $return_commision_param['commision'] = $third_get_point;
	    	    		
	    	    		    if($third_get_point > 0){
			    	    		$thirdMemberSql = "UPDATE member SET commision = commision + ".$third_get_point.", share_point = share_point - ".$third_get_point." WHERE id = ".$user['id'];
			    	    		$this->db->query($thirdMemberSql);
			    	    		
			    	    		//返利记录
			    	    		$this->db->insert('commision_record', $return_commision_param);
	                            $this->db->insert('share_record', $return_share_param);
	    	    		    }
	    	    		}
	    	    		
	    	    		//满三层跳出循环
	    	    		break;
	    	    	}
    	    	}
    	    }
    	    
    	    /*
    	     *      计算上三层佣金返利
    	     *      服务中心拥挤规则，从下往上算  例以3000计算
    	     * 1.	初级150->中级150-高级150
    	     * 2.	初级150->高级300
    	     * 3.	中级300->高级150
    	     * 4.	高级450
    	     * 5.	钱包150=3000*5%
    	     */
    	    $junior_time = 0;
    	    $middle_time = 0;
    	    $advanced_time = 0;
		    for($i=0; $i<count($router_array); $i++){
	        	$user = $this->member_model->getDataById($router_array[$i]);
	        	if($user !== null){
	        		$typeArray = explode(',', $user['type']);
	        	    //如果是服务中心
	        		if(in_array(2, $typeArray)){
    	    			$commision_param = array(
				    	    "level" => $user['level'],
						    "owner_user_id" => $user['id'],
					    	"recharge_user_id" => $recharge_user_id,
    	    			    "order_money" => $total,
    	    			    "pay_money" => $pay_money,
						    "order_id" => $order_id
						);
						
	        		    if(empty($service_center_record)){
	        		        //还没有分配过佣金
	    	    			if($user['level'] == 11){
	    	    				//初级服务中心
	    	    				$commision = $junior_first_commision;
	    	    				$junior_time++;
	    	    			}
	    	    			elseif($user['level'] == 12){
	    	    				//中级服务中心    跳过初级服务中心   吃掉初级第一层的佣金
	    	    				$commision = $junior_first_commision + $middle_first_commision;
	    	    				$middle_time++;
	    	    			}
	    	    		    elseif($user['level'] == 13){
	    	    				//高级服务中心  跳过初级服务中心和中级服务中心    吃掉初级第一层的佣金和中级第一层的佣金
	    	    				$commision = $junior_first_commision + $middle_first_commision + $advanced_first_commision;
	    	    				$advanced_time++;
	    	    			}
	    	    			else{
	    	    				continue;
	    	    			}
	    	    			
	    	    			$service_center_record[] = array(
	    	    			    'commision'=>$commision,
	    	    			    'level'=>$user['level']
	    	    			);
						}
						else{
							$last = count($service_center_record) - 1;
							if($user['level'] >= $service_center_record[$last]['level']){
								//本次等级大于等于上次等级
								if($user['level'] == 11){
									//初级服务中心    这里不可能出现第一次初级服务中心
									if($junior_time == 1){
										//第二次初级服务中心
										$commision = $junior_second_commision;
										$junior_time++;
									}
									elseif($junior_time == 2){
										//第三次初级服务中心
										$commision = $junior_third_commision;
										$junior_time++;
									}
									else{
										continue;
									}
								}
								elseif($user['level'] == 12){
									//中级服务中心
									if($middle_time == 0){
										$commision = $middle_first_commision;
										$middle_time++;
									}
									elseif($middle_time == 1){
										//第二次中级服务中心
										$commision = $middle_second_commision;
										$middle_time++;
									}
									elseif($middle_time == 2){
										//第三次中级服务中心
										$commision = $middle_third_commision;
										$middle_time++;
									}
									else{
										continue;
									}
								}
								elseif($user['level'] == 13){
									//高级服务中心
									if($advanced_time == 0){
										//还没算过高级
										if($service_center_record[$last]['level'] == 11){
											//上次是初级   跳过中级服务中心   吃掉中级第一层的佣金   第一次高级
				    	    				$commision = $middle_first_commision + $advanced_first_commision;
				    	    				$advanced_time++;
										}
										elseif($service_center_record[$last]['level'] == 12){
											//上次是中级   第一次高级
				    	    				$commision = $advanced_first_commision;
				    	    				$advanced_time++;
										}
									}
									elseif($advanced_time == 1){
										//算过一次高级
										$commision = $advanced_senond_commision;
										$advanced_time++;
									}
								    elseif($advanced_time == 2){
										//算过二次高级
										$commision = $advanced_third_commision;
										$advanced_time++;
										break;
									}
								    else{
										continue;
									}
								}
								else{
									continue;
								}
								
								$service_center_record[] = array(
		    	    			    'commision'=>$commision,
		    	    			    'level'=>$user['level']
		    	    			);
							}
							else{
								continue;
							}
						}
						
	        		    if(!isset($commision) || $commision == 0){
							continue;
						}
    	    			
    	    			$commisionSql = "UPDATE member SET commision = commision + ".$commision." WHERE id = ".$user['id'];
    	    			$commision_param['commision'] = $commision;
    	    			$this->db->insert('commision_record', $commision_param);
    	    			$this->db->query($commisionSql);
    	    		}
	        	}
	    	}
    	}
    	
		//自身升级
		if($recharge_user['level'] == 0 && $total >= 1000){
			//被报单1000后    更新自己等级
			$selfSql = "UPDATE member SET level = 1 WHERE id = ".$recharge_user['id'];
			$this->db->query($selfSql);
			
			//升级记录
			$self_upgrade_param = array(
	    	    "old_level" => 0,
			    "new_level" => 1,
		    	"user_id" => $recharge_user['id'],
			    "order_id" => $order_id
			);
			$this->db->insert('upgrade_record', $self_upgrade_param);
			
			
			//自身升级到VIP才有可能计算上级是否符合升级     判断上级是否满足5个VIP  已经是VIP1则不更新
			$parent = $this->member_model->getDataById($recharge_user['parent_id']);
			if($parent !== null){
				$childVipNum = $this->member_model->getChildrenVipNum($recharge_user['parent_id']);
				if($parent['level'] == 1 && $childVipNum >= 5){
					//更新推荐人等级
					$recommendSql = "UPDATE member SET level = 2 WHERE id = ".$recharge_user['parent_id'];
					$this->db->query($recommendSql);
					
					//升级记录
					$parent_upgrade_param = array(
			    	    "old_level" => 1,
					    "new_level" => 2,
				    	"user_id" => $recharge_user['parent_id'],
					    "order_id" => $order_id
					);
					$this->db->insert('upgrade_record', $parent_upgrade_param);
				}
			}
		}
        
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
	
	public function getTakecashPageData($userid, $status, $start, $end, $searchkey, $offset, $num){
	    $where = 'takecash.deleted_at is null';

		//总数
		$this->db->select('count(takecash.id) AS total, sum(takecash.submit_money) as submit_money, sum(takecash.get_money) as get_money');
		$this->db->from('takecash');
		$this->db->join('bank', 'bank.id = takecash.bank_id');
		$this->db->join('member', 'member.id = takecash.user_id');
		
		$this->db->where($where);
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " takecash.created_at >= '".$begin."' AND takecash.created_at <= '".$over."'";
			$this->db->where($time_where);
		}
		
	    if($status != 99){
			$status_where = array(
				"takecash.status" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"takecash.user_id" => $userid
			);
			$this->db->where($user_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('takecash.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		
		
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['total'];
			$submit_money = $data['submit_money'];
			$get_money = $data['get_money'];
		}
		else{
			$count = 0;
			$submit_money = 0;
			$get_money = 0;
		}
		
		//分页
		$this->db->select('takecash.*, bank.user_name, bank.bank_name, bank.card_no, bank.bank_address, member.name, member.commision, member.mobile');
		$this->db->from('takecash');
		$this->db->join('bank', 'bank.id = takecash.bank_id');
		$this->db->join('member', 'member.id = takecash.user_id');
		
		$this->db->where($where);
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " takecash.created_at >= '".$begin."' AND takecash.created_at <= '".$over."'";
			$this->db->where($time_where);
		}
		
	    if($status != 99){
			$status_where = array(
				"takecash.status" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"takecash.user_id" => $userid
			);
			$this->db->where($user_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('takecash.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('takecash.id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $count,
			    "submit_money" => round($submit_money, 2),
			    "get_money" => round($get_money, 2),
		        "data" => $return
		    );
		}
		else{
			return null;
		}
	}
	
	public function deleteTakecashBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('takecash', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function checkTakecash($id, $status) {
		if($status == 1){
		    $data = array(
		        "status" => $status,
				'updated_at' => date('Y-m-d H:i:s', time()),
			);
			
			$this->db->where('id', $id);
			$this->db->update('takecash', $data);
			
			//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
			if($this->db->affected_rows()){
				return true;
			}
			else{
				return false;
			}
		}
		elseif($status == 2){
			$cash = $this->getTakecashById($id);
			if($cash !== null){
				//事务
	            $this->db->trans_begin();
	            
	            $data = array(
			        "status" => $status,
					'updated_at' => date('Y-m-d H:i:s', time()),
				);
				
				$this->db->where('id', $id);
				$this->db->update('takecash', $data);
	            
				//退回钱包
				$commisionSql = "UPDATE member SET commision = commision + ".$cash['submit_money']." WHERE id = ".$cash['user_id'];
	    	    $this->db->query($commisionSql);
	    	    			
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
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	public function getTakecashById($id) {
		$where = array(
			"id" => $id
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('takecash');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function getWalletPageData($userid, $status, $start, $end, $searchkey, $offset, $num){
	    $where = 'wallet_record.deleted_at is null';

		//总数
	    if($status == 99){
	    	//正数
			$this->db->select('count(wallet_record.id) AS total, sum(wallet_record.money) as money');
			$this->db->from('wallet_record');
			$this->db->join('member', 'member.id = wallet_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(1, 2);
			$this->db->where_in("wallet_record.type", $status_where);
			
			if($userid != 'empty'){
				$user_where = array(
					"wallet_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " wallet_record.created_at >= '".$begin."' AND wallet_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('wallet_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				$plusCount = $data['total'];
				$plusMoney = $data['money'];
			}
			else{
				$plusCount = 0;
				$plusMoney = 0;
			}
			
			//负数
	        $this->db->select('count(wallet_record.id) AS total, sum(wallet_record.money) as money');
			$this->db->from('wallet_record');
			$this->db->join('member', 'member.id = wallet_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(
				"wallet_record.type" => 3
			);
			$this->db->where($status_where);
			
			if($userid != 'empty'){
				$user_where = array(
					"wallet_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " wallet_record.created_at >= '".$begin."' AND wallet_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('wallet_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				$minusCount = $data['total'];
				$minusMoney = $data['money'];
			}
			else{
				$minusCount = 0;
				$minusMoney = 0;
			}
	    }
	    else{
	        $this->db->select('count(wallet_record.id) AS total, sum(wallet_record.money) as money');
			$this->db->from('wallet_record');
			$this->db->join('member', 'member.id = wallet_record.user_id');
			
			$this->db->where($where);
		    $status_where = array(
				"wallet_record.type" => $status
			);
			$this->db->where($status_where);
			
			if($userid != 'empty'){
				$user_where = array(
					"wallet_record.user_id" => $userid
				);
				$this->db->where($user_where);
			}
			
		    if($start && $end){
		    	$begin = $start.' 00:00:00';
		    	$over = $end.' 23:59:59';
		    	$time_where = " wallet_record.created_at >= '".$begin."' AND wallet_record.created_at <= '".$over."'";
				$this->db->where($time_where);
			}
	
		    if($searchkey){
		    	$this->db->group_start();
				$this->db->like('wallet_record.order_id', $searchkey);
				$this->db->or_like('member.name', $searchkey);
				$this->db->or_like('member.mobile', $searchkey);
				$this->db->group_end();
			}
			
			
			$queryAll = $this->db->get();
			if ($queryAll->num_rows() > 0){
				$data = $queryAll->row_array();
				if($status == 3){
				    $minusCount = $data['total'];
				    $minusMoney = $data['money'];
				    $plusCount = 0;
				    $plusMoney = 0;
				}
				else{
					$plusCount = $data['total'];
				    $plusMoney = $data['money'];
				    $minusCount = 0;
				    $minusMoney = 0;
				}
			}
			else{
				$plusCount = 0;
				$plusMoney = 0;
				$minusCount = 0;
				$minusMoney = 0;
			}
	    }
		
		//分页
		$this->db->select('wallet_record.*, member.name, member.mobile');
		$this->db->from('wallet_record');
		$this->db->join('member', 'member.id = wallet_record.user_id');
		
		$this->db->where($where);
	    if($status != 99){
			$status_where = array(
				"wallet_record.type" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"wallet_record.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " wallet_record.created_at >= '".$begin."' AND wallet_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('wallet_record.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('wallet_record.id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $plusCount + $minusCount,
		        "plusMoney" => round($plusMoney, 2),
		        "minusMoney" => round($minusMoney, 2),
		        "data" => $return
		    );
		}
		else{
			return null;
		}
	}
	
	public function deleteWalletBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('wallet_record', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getMessagePageData($userid, $status, $time, $searchkey, $offset, $num){
	    $where = 'sms.deleted_at is null';
	    $res_where = array(
			"sms.status" => 1
		);

		//总数
		$this->db->select('count(sms.id) AS total');
		$this->db->from('sms');
		$this->db->join('member', 'member.mobile = sms.mobile', 'left');
		
		$this->db->where($where);
		$this->db->where($res_where);
	    if($status != 99){
			$status_where = array(
				"sms.type" => $status
			);
			$this->db->where($status_where);
		}
		
		if($userid != 'empty'){
			$user_where = array(
				"member.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($time){
	    	$begin = $time.' 00:00:00';
	    	$end = $time.' 23:59:59';
	    	$time_where = " sms.created_at >= '".$begin."' AND sms.created_at <= '".$end."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->or_like('member.name', $searchkey);
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
		$this->db->select('sms.*, member.name');
		$this->db->from('sms');
		$this->db->join('member', 'member.mobile = sms.mobile', 'left');
		
		$this->db->where($where);
		$this->db->where($res_where);
		
	    if($status != 99){
			$status_where = array(
				"sms.type" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"sms.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($time){
	    	$begin = $time.' 00:00:00';
	    	$end = $time.' 23:59:59';
	    	$time_where = " sms.created_at >= '".$begin."' AND sms.created_at <= '".$end."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('sms.id', 'DESC');
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
	
	public function deleteMessageBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('sms', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function insertLogistic($name, $is_lock, $template) {
		$param = array(
		    "name"=> $name,
		    "is_lock"=> $is_lock,
		    "template"=> $template
		);
			
		$this->db->insert('logistic', $param);
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
	public function updateLogistic($id, $name, $is_lock, $template) {
		$param = array(
			"name"=> $name,
		    "is_lock"=> $is_lock,
		    "template"=> $template,
		    "updated_at" => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $id);
		$this->db->update('logistic', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getLogisticPageData($status, $searchkey, $offset, $num){
	    $where = 'deleted_at is null';
	    $status_where = array(
			"is_lock" => $status
		);

		//总数
		$this->db->select('count(id) AS total');
		$this->db->from('logistic');
		
		$this->db->where($where);
		$this->db->where($status_where);

	    if($searchkey){
	    	$this->db->like('name', $searchkey);
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
		$this->db->select('*');
		$this->db->from('logistic');
		
		$this->db->where($where);
		$this->db->where($status_where);

	    if($searchkey){
	    	$this->db->like('name', $searchkey);
		}
		$this->db->order_by('id', 'DESC');
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
	
	public function deleteLogisticBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('logistic', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getLogisticList() {
        $where = 'deleted_at is null AND is_lock = 1';
		$this->db->select('*');
		$this->db->where($where);
	
		$this->db->order_by('id', 'ASC');
		
		$query = $this->db->get('logistic');
	    if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function quickBusiness($company_get_type, $point, $parent_id, $user_id, $order_id, $mark) {
		//事务
        $this->db->trans_begin();
        
        if($company_get_type == 2){
			$useable_param = array(
	    	    "type" => 5,   //1复投 2可用转消费 3复投结算 4订单结算 5商家结算
			    "point" => $point,
		    	"user_id" => $user_id,
			    "order_id" => $order_id,
			    "mark" => $mark
			);
			$this->db->insert('useable_record', $useable_param);
			
			$selfMemberSql = "UPDATE member SET useable_point = useable_point + ".$point." WHERE id = ".$user_id;
        }
        elseif($company_get_type == 3){
        	$com_consume_param = array(
	    	    "type" => 11, //1可用积分转消费积分  2积分转让  3充值报单  4兑换商品   5升级服务中心  6分享  7报单抵扣 8退款  9推荐商家  10兑充升级 11商家结算 12订单结算
	    	    "reason" => 2,  //1转出  2转入
			    "point" => $point,
		    	"user_id" => $user_id,
			    "order_id" => $order_id,
        	    "mark" => $mark
			);
			$this->db->insert('consume_record', $com_consume_param);
			$selfMemberSql = "UPDATE member SET consume_point = consume_point + ".$point." WHERE id = ".$user_id;
        }
		$this->db->query($selfMemberSql);
		
		$consume_point = round(intval($point) * 0.02);
		$consume_param = array(
    	    "type" => 9,  //1可用积分转消费积分  2积分转让  3充值报单  4兑换商品   5升级服务中心  6分享  7报单抵扣 8退款 9推荐商家
		    "point" => $consume_point,
		    "reason" => 2, //1转出  2转入
	    	"user_id" => $parent_id,
		    "order_id" => $order_id
		);
		$this->db->insert('consume_record', $consume_param);
		
		$parentMemberSql = "UPDATE member SET consume_point = consume_point + ".$consume_point." WHERE id = ".$parent_id;
		$this->db->query($parentMemberSql);
		
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
	
	/**
	 * 根据条件跟新单条数据...
	 */
	public function configDeclaration($data) {
		$param = $data;
		if(isset($data['id']) && $data['id']){
			unset($param['id']);
			unset($param['updated_at']);
			unset($param['deleted_at']);
			$param['created_at'] = date('Y-m-d H:i:s',  time());
			$this->db->where('id', $data['id']);
		    $this->db->update('config_declaration', $param);
		}
		else{
			$this->db->insert('config_declaration', $param);
		}
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
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
	
	public function posterData($type) {
        $where = 'deleted_at is null';
        $type_where = array(
			"type" => $type
		);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($type_where);
			
		$query = $this->db->get('poster');
	    if ($query->num_rows() > 0){
			return $query->row_array();
		}
		else{
			return null;
		}
	}
	
	public function editPoster($id, $json_data, $bg_url, $type) {
		$param = array(
		    "json_data"=> $json_data,
		    "type"=> $type
		);
		
		if($bg_url){
			$bg_param = array(
			    "bg_url"=> $bg_url,
			);
			$param = array_merge($param, $bg_param);
		}
		
		if($id){
			$extra_data = array(
			    'updated_at'=>date('Y-m-d H:i:s', time())
			);
			$param = array_merge($param, $extra_data);
			$this->db->where('id', $id);
			$this->db->update('poster', $param);
		}
		else{
			$this->db->insert('poster', $param);
		}
			
		if($this->db->affected_rows()){
		    return true;
		}
		else{
			return null;
		}
	}
	
	public function getMerchantList($searchkey){
	    $where = 'deleted_at is null AND is_company = 1';

		$this->db->select('*');
		$this->db->from('member');
		$this->db->where($where);

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('name', $searchkey);
			$this->db->or_like('mobile', $searchkey);
			$this->db->group_end();
		}
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
	public function getMerchantOrder($user_id, $start, $end){
	    $where = 'orders.deleted_at is null AND orders.status = 3';
	    $user_where = array(
			"goods.company_id" => $user_id
		);

		$this->db->select('orders.goods_model, count(orders.id) AS total, sum(orders.total_point_price) AS point, sum(orders.total_cash_price) AS cash, sum(orders.logistic_fee) AS logistic');
		$this->db->from('orders');
		$this->db->join('goods', 'orders.goods_id = goods.id');
		$this->db->group_by("orders.goods_model");
		$this->db->where($where);
		$this->db->where($user_where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " orders.complete_time >= '".$begin."' AND orders.complete_time <= '".$over."'";
			$this->db->where($time_where);
	    }
	    
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
	public function getMerchantUseablePoint($user_id, $start, $end){
		$type_array = array(4, 5);
	    $where = "deleted_at is null";
	    $user_where = array(
			"user_id" => $user_id
		);

		$this->db->select_sum('point');
		$this->db->from('useable_record');
		$this->db->where($where);
		$this->db->where_in('type', $type_array);
		$this->db->where($user_where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return['point'];
		}
		else{
			return 0;
		}
	}
	
	public function getMerchantConsumePoint($user_id, $start, $end){
		$type_array = array(11, 12);
	    $where = "deleted_at is null ";
	    $user_where = array(
			"user_id" => $user_id
		);

		$this->db->select_sum('point');
		$this->db->from('consume_record');
		$this->db->where($where);
		$this->db->where_in('type', $type_array);
		$this->db->where($user_where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return['point'];
		}
		else{
			return 0;
		}
	}
	
	public function insertCompensation($data) {
		$param = array(
			"limit_point"=>$data['limit_point'],
			"user_id"=>$data['user_id'],
		    "order_id"=>$data['order_id'],
		    "day"=>$data['day'],
		    "rate" => $data['rate'],
		    "begin_time" => $data['begin_time'],
		    "end_time" => $data['end_time'],
		    "type"=>$data['type'], //4补偿
		    "auto_to_consume" => $data['auto_to_consume']
		);
			
		$this->db->insert('repeat_info', $param);
		if($this->db->affected_rows()){
		    return $this->db->insert_id();
		}
		else{
			return null;
		}
	}
	
	public function getRepeatPageData($userid, $start, $end, $searchkey, $offset, $num, $is_delete, $type){
		if($is_delete == 1){
	        $where = 'repeat_info.deleted_at is not null';
		}
		else{
			$where = 'repeat_info.deleted_at is null';
		}

		//总数
		$this->db->select('count(repeat_info.id) AS total');
		$this->db->from('repeat_info');
		$this->db->join('member', 'member.id = repeat_info.user_id');
		
		$this->db->where($where);
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " repeat_info.created_at >= '".$begin."' AND repeat_info.created_at <= '".$over."'";
			$this->db->where($time_where);
		}
		
	    if($type != 99){
			$type_where = array(
				"repeat_info.type" => $type
			);
			$this->db->where($type_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"repeat_info.user_id" => $userid
			);
			$this->db->where($user_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('repeat_info.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
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
		$this->db->select('repeat_info.*, member.name, member.mobile');
		$this->db->from('repeat_info');
		$this->db->join('member', 'member.id = repeat_info.user_id');
		
		$this->db->where($where);
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " repeat_info.created_at >= '".$begin."' AND repeat_info.created_at <= '".$over."'";
			$this->db->where($time_where);
		}
		
	    if($type != 99){
			$type_where = array(
				"repeat_info.type" => $type
			);
			$this->db->where($type_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"repeat_info.user_id" => $userid
			);
			$this->db->where($user_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('repeat_info.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('repeat_info.id', 'DESC');
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
	
	public function deleteRepeatBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('repeat_info', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getSmsTemplateByType($type) {
		$where = "deleted_at is null";
	    $type_where = array(
			"type" => $type
		);

		//总数
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($type_where);

		$query = $this->db->get('sms_template');
		if ($query->num_rows() >= 0){
			return $query->row_array();
		}
		else{
			return null;
		}
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
	
   /**
	 * 根据条件跟新单条数据...
	 */
	public function updateTemplate($data) {
		$param = array(
			'type' => $data['type'],
		    'name' => $data['name'],
		    "content"=> $data['content'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('sms_template', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
   /**
	 * 插入单条记录...
	 */
	public function insertTemplate($data) {
		$param = array(
			'type' => $data['type'],
		    'name' => $data['name'],
		    "content"=> $data['content'],
		);
			
		$this->db->insert('sms_template', $param);
		if($this->db->affected_rows()){
		    return $this->db->insert_id();
		}
		else{
			return null;
		}
	}
	
    public function switchTemplateStatus($data) {
		$param = array(
			'status' => $data['status'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('sms_template', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
    public function selectPageArticle($start, $end, $searchkey, $offset, $num){	
	    $where = 'deleted_at is null';

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		
        if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('title', $searchkey);
			$this->db->or_like('abstracts', $searchkey);
			$this->db->group_end();
		}
		
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
        if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$over."'";
			$this->db->where($time_where);
		}
		
	    if($searchkey){
			$this->db->group_start();
			$this->db->like('title', $searchkey);
			$this->db->or_like('abstracts', $searchkey);
			$this->db->group_end();
		}
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
	
    public function deleteArticleBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('article', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getMerchantRankingOrder($user_id, $start, $end){
	    $where = 'orders.deleted_at is null AND goods.deleted_at is null';
	    $user_where = array(
			"goods.company_id" => $user_id
		);

		$this->db->select('goods.company_id, orders.*');
		$this->db->from('orders');
		$this->db->join('goods', 'orders.goods_id = goods.id');
		$this->db->where($where);
		$this->db->where($user_where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " orders.created_at >= '".$begin."' AND orders.created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
    public function getSaleOrderYearData($year){
    	$status_array = array(1, 2, 3);
    	$begin = $year.'-01-01 00:00:00';
	    $over = $year.'-12-31 23:59:59';    	
	    $where = "deleted_at is null AND created_at >= '".$begin."' AND created_at <= '".$over."' ";
	    
		$this->db->select('*');
		$this->db->from('orders');
		$this->db->where($where);
		$this->db->where_in('status', $status_array);
	    
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
    public function getSaleOrderYearMonthData($year, $month){
    	$status_array = array(1, 2, 3);
    	$month_string = '';
    	if(intval($month) > 10){
    		$month_string = $month;
    	}
    	else{
    		$month_string = '0'.$month;
    	}
    	
    	$begin = $year.'-'.$month_string.'-01 00:00:00';
    	//加一个月
	    $endMonthTimestamp = strtotime("+1 months", strtotime($begin)) - 1;
	    $over = date('Y-m-d H:i:s', $endMonthTimestamp);    	
	    $where = "deleted_at is null AND created_at >= '".$begin."' AND created_at <= '".$over."' ";
		
		$this->db->select('*');
		$this->db->from('orders');
		$this->db->where($where);
		$this->db->where_in('status', $status_array);
	    
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
	public function getOrderUseablePoint($order_id){
		$type_array = array(4, 5);
	    $where = "deleted_at is null";
	    $and_where = array(
			"order_id" => $order_id
		);

		$this->db->select_sum('point');
		$this->db->from('useable_record');
		$this->db->where($where);
		$this->db->where_in('type', $type_array);
		$this->db->where($and_where);
	    
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return['point'];
		}
		else{
			return 0;
		}
	}
	
	public function getOrderConsumePoint($order_id){
		$type_array = array(11, 12);
	    $where = "deleted_at is null ";
	    $and_where = array(
			"order_id" => $order_id
		);

		$this->db->select_sum('point');
		$this->db->from('consume_record');
		$this->db->where($where);
		$this->db->where_in('type', $type_array);
		$this->db->where($and_where);
	    
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return['point'];
		}
		else{
			return 0;
		}
	}
	
	public function message_status($id, $value) {
	    $data = array(
	        "is_message" => $value,
			'updated_at' => date('Y-m-d H:i:s', time()),
		);
		
		$this->db->where('id', $id);
		$this->db->update('member', $data);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getMerchantRankingGoods($user_id, $start, $end){
	    $where = 'deleted_at is null';
	    $user_where = array(
			"company_id" => $user_id
		);

		$this->db->select('*');
		$this->db->from('goods');
		$this->db->where($where);
		$this->db->where($user_where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
	public function getGoodsStatisticPageData($searchkey, $offset, $num, $start, $end, $sort_word, $sort_type){
	    $where = 'orders.deleted_at is null';
		$status_where = array(1, 2, 3);
			
		//总数
		$this->db->select('orders.goods_id, sum(orders.num) as num, sum(case when orders.goods_model=1 then orders.total_point_price else 0 end) as exchange_point, sum(case when orders.goods_model!=1 then orders.total_point_price else 0 end) as consume_point,  sum(orders.total_cash_price) as total_cash_price, sum(orders.logistic_fee) as logistic_fee');
		$this->db->from('orders');
		$this->db->join('goods', 'goods.id = orders.goods_id');
		$this->db->join('member', 'member.id = goods.company_id');
		
		$this->db->group_by("orders.goods_id");
		$this->db->where($where);
		$this->db->where_in('orders.status', $status_where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " orders.created_at >= '".$begin."' AND orders.created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('orders.goods_name', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
	    
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->result_array();
			$count = count($data);
		}
		else{
			$count = 0;
		}
		
		//分页
	    $this->db->select('member.mobile, member.name as username, goods.name as goods_name, orders.goods_id, sum(orders.num) as num, sum(case when orders.goods_model=1 then orders.total_point_price else 0 end) as exchange_point, sum(case when orders.goods_model!=1 then orders.total_point_price else 0 end) as consume_point,  sum(orders.total_cash_price) as total_cash_price, sum(orders.logistic_fee) as logistic_fee');
		$this->db->from('orders');
		$this->db->join('goods', 'goods.id = orders.goods_id');
		$this->db->join('member', 'member.id = goods.company_id');
		$this->db->group_by("orders.goods_id");
		$this->db->where($where);
		$this->db->where_in('orders.status', $status_where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " orders.created_at >= '".$begin."' AND orders.created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('orders.goods_name', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by($sort_word, $sort_type);
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
	
	public function getConsumeStatisticPageData($searchkey, $offset, $num, $start, $end, $sort_word, $sort_type){
	    $where = 'orders.deleted_at is null';
		$status_where = array(1, 2, 3);
			
		//总数
		$this->db->select('orders.user_id, count(orders.id) as num, sum(case when orders.goods_model=1 then orders.total_point_price else 0 end) as exchange_point, sum(case when orders.goods_model!=1 then orders.total_point_price else 0 end) as consume_point,  sum(orders.total_cash_price) as total_cash_price, sum(orders.logistic_fee) as logistic_fee');
		$this->db->from('orders');
		$this->db->join('member', 'member.id = orders.user_id');
		
		$this->db->group_by("orders.user_id");
		$this->db->where($where);
		$this->db->where_in('orders.status', $status_where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " orders.created_at >= '".$begin."' AND orders.created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
	    
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->result_array();
			$count = count($data);
		}
		else{
			$count = 0;
		}
		
		//分页
	    $this->db->select('member.mobile, member.name as username, orders.user_id, count(orders.id) as num, sum(case when orders.goods_model=1 then orders.total_point_price else 0 end) as exchange_point, sum(case when orders.goods_model!=1 then orders.total_point_price else 0 end) as consume_point,  sum(orders.total_cash_price) as total_cash_price, sum(orders.logistic_fee) as logistic_fee');
		$this->db->from('orders');
		$this->db->join('member', 'member.id = orders.user_id');
		$this->db->group_by("orders.user_id");
		$this->db->where($where);
		$this->db->where_in('orders.status', $status_where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " orders.created_at >= '".$begin."' AND orders.created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by($sort_word, $sort_type);
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
	
	public function getDeclarationStatisticPageData($searchkey, $offset, $num, $start, $end, $sort_word, $sort_type){
	    $where = 'recharge_record.deleted_at is null AND recharge_record.status = 1';
			
		//总数
		$this->db->select('count(recharge_record.id) as num, sum(recharge_record.recharge_money) as recharge_money, sum(point) as point, sum(recharge_record.pay_money) as pay_money');
		$this->db->from('recharge_record');
		$this->db->join('member', 'member.id = recharge_record.service_user_id');
		
		$this->db->group_by("recharge_record.service_user_id");
		$this->db->where($where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " recharge_record.created_at >= '".$begin."' AND recharge_record.created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
	    
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->result_array();
			$count = count($data);
		}
		else{
			$count = 0;
		}
		
		//分页
	    $this->db->select('member.mobile, member.name as username, recharge_record.service_user_id, count(recharge_record.id) as num, sum(recharge_record.recharge_money) as recharge_money, sum(point) as point, sum(recharge_record.pay_money) as pay_money');
		$this->db->from('recharge_record');
		$this->db->join('member', 'member.id = recharge_record.service_user_id');
		$this->db->group_by("recharge_record.service_user_id");
		$this->db->where($where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " recharge_record.created_at >= '".$begin."' AND recharge_record.created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by($sort_word, $sort_type);
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
	
	public function getMemberStatisticPageData($searchkey, $offset, $num, $start, $end, $sort_word, $sort_type){
	    $where = 'deleted_at is null';
			
		//总数
		$this->db->select('count(id) as num, sum(consume_point) as total_consume_point, sum(exchange_point) as total_exchange_point, sum(useable_point) as total_useable_point, sum(wait_point) as total_wait_point, sum(share_point) as total_share_point, sum(commision) as total_commision');
		$this->db->from('member');
		$this->db->where($where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('name', $searchkey);
			$this->db->or_like('mobile', $searchkey);
			$this->db->group_end();
		}
	    
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$total = $queryAll->row_array();
			$count = $total['num'];
		}
		else{
			$count = 0;
		}
		
		//分页
	    $this->db->select('*');
		$this->db->from('member');
		$this->db->where($where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('name', $searchkey);
			$this->db->or_like('mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by($sort_word, $sort_type);
	    $this->db->limit($num, $offset);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $count,
		        "total" => $total,
		        "data" => $return
		    );
		}
		else{
			return null;
		}
	}
	
	public function getSharemanagerPageData($userid, $status, $start, $end, $searchkey, $offset, $num){
	    $where = 'share_goods.deleted_at is null';

		//总数
	    $this->db->select('count(share_goods.id) AS total');
		$this->db->from('share_goods');
		$this->db->join('member', 'member.id = share_goods.user_id');
		
		$this->db->where($where);
		
	    if($status != 99){
			$status_where = array(
				"share_goods.status" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"share_goods.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " share_goods.created_at >= '".$begin."' AND share_goods.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('share_goods.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
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
		$this->db->select('share_goods.*, member.name, member.mobile');
		$this->db->from('share_goods');
		$this->db->join('member', 'member.id = share_goods.user_id');
		
		$this->db->where($where);
	    if($status != 99){
			$status_where = array(
				"share_goods.status" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty'){
			$user_where = array(
				"share_goods.user_id" => $userid
			);
			$this->db->where($user_where);
		}
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " share_goods.created_at >= '".$begin."' AND share_goods.created_at <= '".$over."'";
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('share_goods.order_id', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('share_goods.id', 'DESC');
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
	
	public function deleteSharemanagerBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('share_goods', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function selectShareOrderById($id) {
		$where = array(
			"orders.share_id" => $id
		);
		$and_where = 'orders.deleted_at is null';
		$status_where = array(1, 2, 3);
		
		$query = $this->db->select('orders.*, member.mobile, member.name')
		    ->from('orders')
		    ->join('member', 'member.id = orders.user_id')
		    ->where($where)
		    ->where($and_where)
		    //->where_in('orders.status', $status_where)
		    ->get();
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
}