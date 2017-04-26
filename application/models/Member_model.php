<?php
class Member_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function dologin($data) {
		$newPwd = $this->password($data['password']);
		$where = array(
			"mobile" => $data['username'],
			"password" => $newPwd,
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('member');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	$typeArray = explode(',', $result['type']);

	    	if(in_array('1', $typeArray)){
		    	if($result['openid'] && $data['wechat']){
		    		//更新
		    	    $res = $this->updateWechatInfo($result, $data);
		    	    if($res && isset($data['openid']) && $data['openid']){
		    	    	$result['openid'] = $data['openid'];
		    	    }
		    	    if($res && isset($data['headimgurl']) && $data['headimgurl']){
		    	    	$result['headimgurl'] = $data['headimgurl'];
		    	    }
		    	}
		    	elseif(isset($data['openid']) && $data['openid'] && $data['wechat']){
		    		//第一次绑定
		    		$res = $this->bindWechatInfo($result, $data);
		    		if($res){
		    			$result['openid'] = $data['openid'];
		    		}
		    	    if($res && isset($data['headimgurl']) && $data['headimgurl']){
		    	    	$result['headimgurl'] = $data['headimgurl'];
		    	    }
		    	}
		    	else{
		    		$this->updateLoginTime($result['id']);
		    	}
		    	return $result;
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
	
	public function second_password($original){
		$string = "goodluck_second";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
	}
	
   /**
	 * 修改密码...
	 */
	public function submit_chgpwd($data) {
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
	 * 修改二级密码...
	 */
	public function submit_second_chgpwd($data) {
		$param = array(
			'exchange_pwd' => $this->second_password($data['newpassword']),
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $data['id']);
		if($data['password']){
			//原始二级密码不为空时
		    $this->db->where('exchange_pwd', $this->second_password($data['password']));
		}
		else{
			$this->db->where('exchange_pwd', '');
		}
		$this->db->update('member', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	
	public function submit_regist($data) {
		//parent_mobile 为上级手机号
		$my_invest_code = $this->random_str(6);
	    $original_parentid = $this->config->item('original_parentid');

    	$param = array(
		    "mobile" => $data['mobile'],
			"password" => $this->password($data['password']),
		    "code" => $my_invest_code
		);
		
	    //事务
        $this->db->trans_begin();
        $this->db->insert('member', $param);
        $user_id = $this->db->insert_id();
        
	    $recommend_user = $this->getDataByMobile($data['parent_mobile']);
    	if($recommend_user === null){
    		$parent_id = $original_parentid;
    		$router = $parent_id.','.$user_id;
    		$parent = $this->getDataById($parent_id);
    		$data['parent_mobile'] = $parent !== null ? $parent['mobile'] : '--';
    	}
    	else{
    		$parent_id = $recommend_user['id'];
    		$router = $recommend_user['router']. "," . $user_id;
    	}
    	
    	$update_param = array(
    	    "parent_mobile"=>$data['parent_mobile'],
		    "parent_id"=>$parent_id,
		    "router"=>$router,
		);
        
    	//更新member表的router数据
        $this->db->where('id', $user_id);
        $this->db->update('member', $update_param);
            
	    if ($this->db->trans_status() === FALSE)
		{
			// 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
			$this->db->trans_rollback();
			return null;
		}
		else
		{
			$this->db->trans_commit();
		    return $user_id;
		}
	}
	
    /**
     *  生成指定长度的随机字符串(包含大写英文字母, 小写英文字母, 数字)
     * @param int $length 需要生成的字符串的长度
     * @return string 包含 大小写英文字母 和 数字 的随机字符串
     */
    public function random_str($length)
    {
        //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++)
        {
            $rand = mt_rand(0, $arr_len-1);
            $str.=$arr[$rand];
        }

        return $str;
    }
    
	public function getDataByMobile($mobile) {
		$where = array(
			"mobile" => $mobile
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
	
	public function getDataById($id) {
		$where = array(
			"id" => $id
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
	
	public function submit_personal($data) {
		$param = array(
			'name' => $data['name'],
			'card_no' => $data['card_no'],
			'sex' => $data['sex'],
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $data['id']);
		$this->db->update('member', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	
	public function getDirectChildrenById($page, $num, $userid) {	
		$offset = ($page - 1) * $num;
		$where = array(
			"parent_id" => $userid
		);
	    $and_where = 'deleted_at is null';

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('member');
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
		
		$query = $this->db->get('member');
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
	
	public function selectPageData($manager, $company, $level, $time, $searchkey, $offset, $num){	
	    $where = 'deleted_at is null';

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		
	    if($time){
	    	$begin = $time.' 00:00:00';
	    	$end = $time.' 23:59:59';
	    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$end."'";
			$this->db->where($time_where);
		}
		
		if($level != 99){
			$level_where = array(
				"level" => $level
			);
			$this->db->where($level_where);
		}
		
	    if($manager == 1){
			$manager_where = array(
				"is_manager" => 1
			);
			$this->db->where($manager_where);
		}
		
	    if($company == 1){
			$company_where = array(
				"is_company" => 1
			);
			$this->db->where($company_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('name', $searchkey);
			$this->db->or_like('mobile', $searchkey);
			$this->db->group_end();
		}
		
		$queryAll = $this->db->get('member');
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
	    if($time){
	    	$begin = $time.' 00:00:00';
	    	$end = $time.' 23:59:59';
	    	$time_where = " created_at >= '".$begin."' AND created_at <= '".$end."'";
			$this->db->where($time_where);
		}
		
	    if($level != 99){
			$level_where = array(
				"level" => $level
			);
			$this->db->where($level_where);
		}
		
	    if($manager == 1){
			$manager_where = array(
				"is_manager" => 1
			);
			$this->db->where($manager_where);
		}
		
	    if($company == 1){
			$company_where = array(
				"is_company" => 1
			);
			$this->db->where($company_where);
		}

	    if($searchkey){
			$this->db->group_start();
			$this->db->like('name', $searchkey);
			$this->db->or_like('mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('member');
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
	
	public function deleteBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('member', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getDirectChildrenNumById($userid) {	
		$where = array(
			"parent_id" => $userid
		);
	    $and_where = 'deleted_at is null';

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('member');
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['total'];
		}
		else{
			$count = 0;
		}
		
		return $count;
	}
	
	public function updateLoginTime($id) {
		$param = array(
		    'last_login_time' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $id);
		$this->db->update('member', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function bindWechatInfo($userInfo, $data) {
		$param = array(
		    'openid'=>$data['openid'],
			'sex'=>$data['sex'],
			'province'=>$data['province'],
			'city'=>$data['city'],
			'country'=>$data['country'],
		    'nickname'=>$data['nickname'],
			'headimgurl'=>$data['headimgurl'],
		    'business_bind_time' => date('Y-m-d H:i:s', time()),
		    'last_login_time' => date('Y-m-d H:i:s', time())
		);
		
		if(!$userInfo['name']){
			$extra_param = array(
			    'name'=>$data['nickname']
			);
			$param = array_merge($param, $extra_param);
		}
		
		$this->db->where('id', $userInfo['id']);
		$this->db->update('member', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function updateWechatInfo($userInfo, $data) {
		$param = array(
		    'openid'=>$data['openid'],
			'sex'=>$data['sex'],
			'province'=>$data['province'],
			'city'=>$data['city'],
			'country'=>$data['country'],
		    'nickname'=>$data['nickname'],
			'headimgurl'=>$data['headimgurl'],
		    'last_login_time' => date('Y-m-d H:i:s', time())
		);
		
	    if(!$userInfo['name']){
			$extra_param = array(
			    'name'=>$data['nickname']
			);
			$param = array_merge($param, $extra_param);
		}
		
		$this->db->where('id', $userInfo['id']);
		$this->db->update('member', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function insertBankInfo($userid, $bank_name, $username, $card_no, $bank_address) {
		$param = array(
			'user_id' => $userid,
		    'user_name' => $username,
		    'bank_name' => $bank_name,
		    'card_no' => $card_no,
		    'bank_address' => $bank_address,
		);
			
		$this->db->insert('bank', $param);
		if($this->db->affected_rows()){
		    return $this->db->insert_id();
		}
		else{
			return null;
		}
	}
	
	public function getBankList($user_id) {
		$where = array(
			"user_id" => $user_id
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('bank');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function deleteBankInfo($id, $user_id){
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $id);
		$this->db->where('user_id', $user_id);
		$this->db->update('bank', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getDeclarationTotalByUserd($userid) {	
		$where = array(
			"service_user_id" => $userid
		);
	    $and_where = 'deleted_at is null';

		//总数
		$this->db->select_sum('pay_money');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('recharge_record');
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['pay_money'];
		}
		else{
			$count = 0;
		}
		
		return $count;
	}
	
	public function getChargeTotalByUserd($userid) {	
		$where = array(
			"recharge_user_id" => $userid
		);
	    $and_where = 'deleted_at is null';

		//总数
		$this->db->select_sum('recharge_money');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('recharge_record');
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['recharge_money'];
		}
		else{
			$count = 0;
		}
		
		return $count;
	}
	
	public function getCashTotalByUserd($userid) {	
		$where = array(
			"user_id" => $userid
		);
	    $and_where = 'deleted_at is null';

		//总数
		$this->db->select('type, money');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('cash_record');
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->result_array();
			$count = 0;
			foreach($data as $item){
				if($item['type'] != 4){
					$count = $count + $item['money'];
				}
				else{
					$count = $count - $item['money'];
				}
			}
		}
		else{
			$count = 0;
		}
		
		return $count;
	}
	
	public function changeRecommend($id, $recommend) {
		$self = $this->getDataById($id);
		if($self !== null){
			$new_router = $recommend['router'].','.$id;
			$children = $this->selectAllChildren($self['router']);
			
			//事务
			$this->db->trans_begin();
			$param = array(
			    "parent_id" => $recommend['id'],
				"parent_mobile" => $recommend['mobile'],
				"router" => $new_router,
			    "updated_at" => date('Y-m-d H:i:s', time())
			);
			
			$this->db->where('id', $id);
			$this->db->update('member', $param);
			
			if($children !== null){
				//修改下级的router
				$length = strlen($self['router']);
				foreach($children as $item){
					$rest_router = substr($item['router'], $length);  //截取剩余的
					$paramUpdate = array(
					    "router" => $new_router.$rest_router,
					);
					
					$this->db->where('id', $item['id']);
				    $this->db->update('member', $paramUpdate);
				}
			}
			
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
	
	public function upgradeLevel($user_id, $parent_id, $pay_consume_rate, $old_level, $new_level, $order_id, $schedule, $commisionSetting) {
		$to_upgrade_user = $this->getDataById($user_id);
		if($to_upgrade_user){
			//事务
	        $this->db->trans_begin();
	        $flag = true;
		    
			//购物券
			$paramExchange = array(
				"type" => 3, //1充值  2兑换商品 3升级到服务中心
		    	"user_id" => $user_id,
	    	    "order_id" => $order_id
			);
			
			//生成现金记录
			$paramCash = array(
				"type" => 3,  //1兑换商品 2服务中心充值 3升级服务中心
			    "pay_no" => 'pay_under_line', //转出
		    	"user_id" => $user_id,
			    "order_id" => $order_id
			);
			
			//消费积分记录
			$consume_param = array(
	    	    "type" => 5, //1可用积分转消费积分  2消费积分转入 3充值    4兑换商品  5升级服务中心
	    	    "reason" => 2,  //1转出  2转入
		    	"user_id" => $user_id,
			    "order_id" => $order_id
			);
			
			//待用积分记录
			$active_timestamp = time() + $schedule['wait_period'] * 24 * 60 * 60;
	        $active_time = date('Y-m-d H:i:s', $active_timestamp);
			$wait_param = array(
			    "multiple" => $schedule['wait_rate'],
	    	    "day" => $schedule['wait_period'],
		    	"active_time" => $active_time,
			    "type" => 3,   //3升级服务中心
		    	"user_id" => $user_id,
	    	    "order_id" => $order_id
			);
			
			//分享积分记录
			$share_param = array(
	    	    "type" => 3,  //升级到服务中心
		    	"user_id" => $user_id,
			    "order_id" => $order_id
			);
			
			//升级记录
			$upgrade_param = array(
	    	    "old_level" => $old_level,
			    "new_level" => $new_level,
		    	"user_id" => $user_id,
			    "order_id" => $order_id
			);
			
			if($new_level == 11){
				//升级到初级服务中心
				if($old_level == 0 || $old_level == 1 || $old_level == 2){
					$money = 30000;
				}
				else{
					return false;
				}
			}
			elseif($new_level == 12){
				//升级到中级服务中心
				if($old_level == 0 || $old_level == 1 || $old_level == 2){
					//直升中级
					$money = 100000;
					$exchange_point = $money;
				}
				elseif($old_level == 11){
					//初级升中级
					$money = 70000;
					$exchange_point = $money;
				}
			}
		    elseif($new_level == 13){
				//升级到高级服务中心
		        if($old_level == 0 || $old_level == 1 || $old_level == 2){
					//直升高级
					$money = 300000;
					$exchange_point = $money;
				}
				elseif($old_level == 11){
					//初级升高级
					$money = 270000;
					$exchange_point = $money;
				}
		        elseif($old_level == 12){
					//中级升高级
					$money = 200000;
					$exchange_point = $money;
				}
			}
			else{
				return false;
			}
				
			if($pay_consume_rate > 0 && $pay_consume_rate <= 100){
				//消费积分记录
				$flag = false;
				$upgradePoint = $money * $pay_consume_rate * 0.01;
				$consume_upgrade_param = array(
		    	    "type" => 10, //1可用积分转消费积分  2消费积分转入 3充值    4兑换商品  5升级服务中心   10兑充升级
		    	    "reason" => 1,  //1转出  2转入
				    "point" => $upgradePoint,
			    	"user_id" => $parent_id,
				    "order_id" => $order_id
				);
				$upgradeMemberSql = "UPDATE member SET consume_point = consume_point - ".$upgradePoint." WHERE id = ".$parent_id. " AND consume_point >= ".$upgradePoint;
				$this->db->query($upgradeMemberSql);
		        
		        if($this->db->affected_rows()){
			        $flag = true;
			        $this->db->insert('consume_record', $consume_upgrade_param);
		        }
		        else{
		        	$flag = false;
		        }
			}
	    
			if($flag){
		        $final_money = $money * (100 - $pay_consume_rate) * 0.01; //除去抵扣积分后的钱	
				$consume_point = $money * $schedule['give_consume_rate'] * 0.01;
				$shoot_point = $money;
				$share_point = $money * $schedule['give_share_rate'] * 0.01;
				$future_point = $shoot_point * $schedule['wait_rate'];
				
				$paramCash['money'] = $final_money;  //除去抵扣积分后的钱
				$consume_param['point'] = $consume_point;
				$wait_param['shoot_point'] = $shoot_point;
				$wait_param['future_point'] = $future_point;
				$share_param['point'] = $share_point;
				if($new_level == 11){
					//用户信息
					$memberSql = "UPDATE member SET consume_point = consume_point + ".$consume_point.", wait_point = wait_point + ".$future_point.", share_point = share_point + ".$share_point.", type='1,2', level=".$new_level." WHERE id = ".$user_id;
				}
				else{
					//用户信息
					$memberSql = "UPDATE member SET consume_point = consume_point + ".$consume_point.", wait_point = wait_point + ".$future_point.", share_point = share_point + ".$share_point.", exchange_point = exchange_point + ".$exchange_point.", type='1,2', level=".$new_level." WHERE id = ".$user_id;
					$paramExchange['point'] = $exchange_point;
				}
				
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
							"user_id" => $user_id,
			    		    "rate" => $charge_repeat_rate_arr[$i],
			    		    "day" => $charge_repeat_peroid_arr[$i],
						    "already_shot_point" => 0,
			    		    "type" => 2, //1报单 2升级 3商家 4补偿
						    "limit_point" => $limit_point,
				    		"begin_time" => date('Y-m-d H:i:s', $begin_time),
				    		"end_time" => date('Y-m-d H:i:s', $end_time)
						);
		    		}
		    		
			    	if(!empty($repeatParam)){
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
		    	}
		    	/*复投设置end*/
				
				$junior_first_commision = $final_money * $commisionSetting['junior_first'] * 0.01;
	    	    $junior_second_commision = $final_money * $commisionSetting['junior_second'] * 0.01;
	    	    $junior_third_commision = $final_money * $commisionSetting['junior_third'] * 0.01;
	    	    $middle_first_commision = $final_money * $commisionSetting['middle_first'] * 0.01;
	    	    $middle_second_commision = $final_money * $commisionSetting['middle_second'] * 0.01;
	    	    $middle_third_commision = $final_money * $commisionSetting['middle_third'] * 0.01;
	    	    $advanced_first_commision = $final_money * $commisionSetting['advanced_first'] * 0.01;
	    	    $advanced_second_commision = $final_money * $commisionSetting['advanced_second'] * 0.01;
	    	    $advanced_third_commision = $final_money * $commisionSetting['advanced_third'] * 0.01;
	    	    
				$router_array = explode(',', $to_upgrade_user['router']);
	    	    $last = count($router_array) - 1;
	    	    unset($router_array[$last]);
	    	    $router_array = array_reverse($router_array);
	    	    $service_center_record = array();
	    	    $total = $money;
	
			    /*
	    	     * 计算上两层会员返利
	    	     */
	    	    for($i=0; $i<count($router_array); $i++){
	    	    	$user = $this->getDataById($router_array[$i]);
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
					    	"recharge_user_id" => $user_id,
	        			    "order_money" => $total,
	        			    "pay_money" => $final_money,
						    "order_id" => $order_id,
	        			    "type" => 1
						);
						
		    	    	if($i == 0){
		    	    		//上面一层会员（服务中心也是会员）
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
		    	    		//上面二层会员，必须为VIP1
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
	    	     *      服务中心拥挤规则，从下往上算  例以3000计算佣金
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
						    	"recharge_user_id" => $user_id,
	    	    			    "order_money" => $money,
	    	    			    "pay_money" => $final_money,
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
	
		        $this->db->query($memberSql);
				$this->db->insert('cash_record', $paramCash);
		        $this->db->insert('shoot_record', $wait_param);
		        $this->db->insert('consume_record', $consume_param);
		        $this->db->insert('share_record', $share_param);
		        $this->db->insert('upgrade_record', $upgrade_param);
		        if($new_level == 12 || $new_level == 13){
		        	//只有中高级服务中心才有送购物券
		        	$this->db->insert('exchange_record', $paramExchange);
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
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	
	public function upgrade($user_id, $money, $order_id){
		$user = $this->getDataById($user_id);
		if($user !== null){
			//自身升级
			if($user['level'] == 0 && $money >= 1000){
				//事务
		        $this->db->trans_begin();
		        
				//被报单1000后    更新自己等级
				$selfSql = "UPDATE member SET level = 1 WHERE id = ".$user_id;
				$this->db->query($selfSql);
				
				//升级记录
				$self_upgrade_param = array(
		    	    "old_level" => 0,
				    "new_level" => 1,
			    	"user_id" => $user_id,
				    "order_id" => $order_id
				);
				$this->db->insert('upgrade_record', $self_upgrade_param);
				
				
				//自身升级到VIP才有可能计算上级是否符合升级     判断上级是否满足5个VIP  已经是VIP1则不更新
				$parent = $this->getDataById($user['parent_id']);
				if($parent !== null){
					$childVipNum = $this->getChildrenVipNum($user['parent_id']);
					if($parent['level'] == 1 && $childVipNum >= 5){
						//更新推荐人等级
						$recommendSql = "UPDATE member SET level = 2 WHERE id = ".$user['parent_id'];
						$this->db->query($recommendSql);
						
						//升级记录
						$parent_upgrade_param = array(
				    	    "old_level" => 1,
						    "new_level" => 2,
					    	"user_id" => $user['parent_id'],
						    "order_id" => $order_id
						);
						$this->db->insert('upgrade_record', $parent_upgrade_param);
					}
				}
				
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
	
	public function getChildrenVipNum($user_id) {	
		$where = array(
			"parent_id" => $user_id
		);
	    $and_where = 'deleted_at is null AND level >= 1';

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('member');
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['total'];
		}
		else{
			$count = 0;
		}
		
		return $count;
	}
	
	public function insertImportUser($data, $parent) {
		foreach($data as $key=>$item){
			if($item == ''){
				$data[$key] = null;
			}
		}
		
		$param = array(
		    'name' => $data[1],
		    'mobile' => $data[2],
		    'password' => $this->password('123456'),
			'parent_id' => $parent['id'],
		    "parent_mobile"=>$parent['mobile']
		);
		
		$this->db->trans_begin();
        $this->db->insert('member', $param);
        $user_id = $this->db->insert_id();
        
        $router = $parent['router'].','.$user_id;
        $update_param = array(
		    "router"=>$router
		);
        
    	//更新member表的router数据
        $this->db->where('id', $user_id);
        $this->db->update('member', $update_param);
        
	    if ($this->db->trans_status() === FALSE)
		{
			// 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
			$this->db->trans_rollback();
			return null;
		}
		else
		{
			$this->db->trans_commit();
		    return $user_id;
		}
	}
	
	public function selectAllChildren($router) {
    	$sql = 'SELECT * FROM member WHERE router REGEXP \'^' . $router . '(,[0-9]+){1,}$\'';
		$query = $this->db->query($sql);
	    if ($query->num_rows() > 0){
		    return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function selectAllChildrenContainSelf($router) {
    	$sql = 'SELECT id FROM member WHERE router REGEXP \'^' . $router . '(,[0-9]+){0,}$\'';
		$query = $this->db->query($sql);
	    if ($query->num_rows() > 0){
		    return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	/*
	public function selectAllData() {
    	$sql = 'SELECT * FROM member WHERE 1=1';
		$query = $this->db->query($sql);
	    if ($query->num_rows() > 0){
		    return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function updateParentMobile($id, $mobile) {
		$param = array(
			'parent_mobile' => $mobile,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $id);
		$this->db->update('member', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return null;
		}
	}
	*/
	
	public function submit_forget($data) {
		$param = array(
			'password' => $this->password($data['password']),
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('mobile', $data['mobile']);
		$this->db->update('member', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			$where = array(
				"mobile" => $data['mobile'],
				"password" => $this->password($data['password']),
			);
			
			$and_where = 'deleted_at is null';
			
			$query = $this->db->select('*')
			    ->where($where)
			    ->where($and_where)
			    ->get('member');
		
		    if ($query->num_rows() > 0){
		    	$result = $query->row_array();
		    	$this->updateLoginTime($result['id']);
		    	return $result;
		    }
		    else{
		    	return 'update_success';
		    }
		}
		else{
			return 'update_fail';
		}
	}
	
	public function submit_forget_second($data) {
		$newpwd = $this->second_password($data['password']);
		$param = array(
			'exchange_pwd' => $newpwd,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $data['id']);
		$this->db->update('member', $param);
	
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return $newpwd;
		}
		else{
			return false;
		}
	}
	
	public function do_get_cash($money, $get_money, $user_id, $bank_id, $order_id, $tax_money, $rate) {
		//事务
		$this->db->trans_begin();
		$param = array(
		    "user_id" => $user_id,
		    "order_id" => $order_id,
			"tax_money" => $tax_money,
			"rate" => $rate,
		    "status" => 0,
			"submit_money" => $money,
			"get_money" => $get_money,
		    "bank_id" => $bank_id
		);
		
		$this->db->insert('takecash', $param);
		$memberSql = "UPDATE member SET commision = commision - ".$money." WHERE id = ".$user_id;
		$this->db->query($memberSql);

		
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
	
	public function cashlog_data_by_page($user_id, $page, $num) {	
		$where = array(
			"user_id" => $user_id
		);
		$offset = ($page - 1) * $num;
	    $and_where = 'deleted_at is null';

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('takecash');
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['total'];
		}
		else{
			$count = 0;
		}
		
		//分页
		$page_where = array(
			"takecash.user_id" => $user_id
		);
	    $page_and_where = 'takecash.deleted_at is null';
	    
		$this->db->select('takecash.*, bank.bank_name, bank.card_no, bank.user_name');
		$this->db->from('takecash');
		$this->db->join('bank', 'takecash.bank_id = bank.id');
		$this->db->where($page_where);
		$this->db->where($page_and_where);

		$this->db->order_by('takecash.id', 'DESC');
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
	
    public function selectTakecashLog($user_id) {
		$begin = date('Y-m', time()).'-01 00:00:00';
		$endTimestamp = strtotime("+1 months", strtotime($begin)) - 1;
		$end = date('Y-m-d H:i:s', $endTimestamp);
		$status_where = array(0, 1);
		$and_where = "deleted_at is null AND created_at >= '".$begin."' AND created_at <= '".$end."'";
		$user_where = array(
			"user_id" => $user_id
		);
		
		$query = $this->db->select('*')
		    ->where_in('status', $status_where)
		    ->where($and_where)
		    ->where($user_where)
		    ->get('takecash');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
    public function getMonthCommision($user_id) {
		$begin = date('Y-m', time()).'-01 00:00:00';
		$endTimestamp = strtotime("+1 months", strtotime($begin)) - 1;
		$end = date('Y-m-d H:i:s', $endTimestamp);
		$where = "deleted_at is null AND created_at >= '".$begin."' AND created_at <= '".$end."'";
		$and_where = array(
			"owner_user_id" => $user_id
		);
		
		$query = $this->db->select_sum('commision')
		    ->where($where)
		    ->where($and_where)
		    ->get('commision_record');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result['commision'];
		}
		else{
			return 0;
		}
	}
	
    public function getTotalCommision($user_id) {
		$where = "deleted_at is null";
		$and_where = array(
			"owner_user_id" => $user_id
		);
		
		$query = $this->db->select_sum('commision')
		    ->where($where)
		    ->where($and_where)
		    ->get('commision_record');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result['commision'];
		}
		else{
			return 0;
		}
	}
	
    public function getMonthConsume($user_id) {
		$begin = date('Y-m', time()).'-01 00:00:00';
		$endTimestamp = strtotime("+1 months", strtotime($begin)) - 1;
		$end = date('Y-m-d H:i:s', $endTimestamp);
		
		$whereConsume = "deleted_at is null AND created_at >= '".$begin."' AND created_at <= '".$end."' AND type IN (1, 2)";
		$whereRefund = "deleted_at is null AND created_at >= '".$begin."' AND created_at <= '".$end."' AND type = 3";
		
		$and_where = array(
			"user_id" => $user_id
		);
		
		$queryConsume = $this->db->select_sum('money')
		    ->where($whereConsume)
		    ->where($and_where)
		    ->get('wallet_record');
		
		$queryRefund = $this->db->select_sum('money')
		    ->where($whereRefund)
		    ->where($and_where)
		    ->get('wallet_record');
		
	    if ($queryConsume->num_rows() > 0){
	    	$resultConsume = $queryConsume->row_array();
	    	$totalConsume = $resultConsume['money'];
		}
		else{
			$totalConsume = 0;
		}
		
        if ($queryRefund->num_rows() > 0){
	    	$resultRefund = $queryRefund->row_array();
	    	$totalRefund = $resultRefund['money'];
		}
		else{
			$totalRefund = 0;
		}
		
		$diff = $totalConsume - $totalRefund;
		return $diff;
	}
	
    public function getTotalConsume($user_id) {
		$whereConsume = "deleted_at is null AND type IN (1, 2)";
		$whereRefund = "deleted_at is null AND type = 3";
		$and_where = array(
			"user_id" => $user_id
		);
		
		$queryConsume = $this->db->select_sum('money')
		    ->where($whereConsume)
		    ->where($and_where)
		    ->get('wallet_record');
		
		$queryRefund = $this->db->select_sum('money')
		    ->where($whereRefund)
		    ->where($and_where)
		    ->get('wallet_record');
		
	    if ($queryConsume->num_rows() > 0){
	    	$resultConsume = $queryConsume->row_array();
	    	$totalConsume = $resultConsume['money'];
		}
		else{
			$totalConsume = 0;
		}
		
        if ($queryRefund->num_rows() > 0){
	    	$resultRefund = $queryRefund->row_array();
	    	$totalRefund = $resultRefund['money'];
		}
		else{
			$totalRefund = 0;
		}
		
		$diff = $totalConsume - $totalRefund;
		return $diff;
	}
	
    public function getTotalTakecash($user_id) {
		$where = "deleted_at is null";
		$and_where = array(
			"user_id" => $user_id
		);
		
		$query = $this->db->select_sum('submit_money')
		    ->where($where)
		    ->where($and_where)
		    ->get('takecash');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result['submit_money'];
		}
		else{
			return 0;
		}
	}
	
	public function consumelog_data_by_page($user_id, $page, $num, $categoty) {	
		$where = array(
			"user_id" => $user_id
		);
		$offset = ($page - 1) * $num;
	    $and_where = 'deleted_at is null';

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);
		
	    if($categoty != 'total'){
			$begin = date('Y-m', time()).'-01 00:00:00';
			$endTimestamp = strtotime("+1 months", strtotime($begin)) - 1;
			$end = date('Y-m-d H:i:s', $endTimestamp);
			$time_where = "created_at >= '".$begin."' AND created_at <= '".$end."'";
			$this->db->where($time_where);
		}

		$queryAll = $this->db->get('wallet_record');
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['total'];
		}
		else{
			$count = 0;
		}
		
		//分页
		$this->db->select('*');
		$this->db->from('wallet_record');
		$this->db->where($where);
		$this->db->where($and_where);
		
	    if($categoty != 'total'){
			$begin = date('Y-m', time()).'-01 00:00:00';
			$endTimestamp = strtotime("+1 months", strtotime($begin)) - 1;
			$end = date('Y-m-d H:i:s', $endTimestamp);
			$time_where = "created_at >= '".$begin."' AND created_at <= '".$end."'";
			$this->db->where($time_where);
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
}
	