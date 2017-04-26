<?php
class Service_model extends CI_Model {
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

	    	if(in_array('2', $typeArray)){
		        if($result['service_openid'] && $data['wechat']){
		        	//更新
		    	    $res = $this->updateWechatInfo($result, $data);
		            if($res && isset($data['openid']) && $data['openid']){
		    	    	$result['service_openid'] = $data['openid'];
		    	    }
		            if($res && isset($data['headimgurl']) && $data['headimgurl']){
		    	    	$result['headimgurl'] = $data['headimgurl'];
		    	    }
		    	}
		    	elseif(isset($data['openid']) && $data['openid'] && $data['wechat']){
		    		//第一次绑定
		    		$res = $this->bindWechatInfo($result, $data);
		    	    if($res){
		    			$result['service_openid'] = $data['openid'];
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
	    	elseif(in_array('1', $typeArray)){
	    		return 'no_privilege';
	    	}
	    	else{
	    		return null;
	    	}
		}
		else{
			return null;
		}
	}
	
	public function password($original){
		$string = "goodluck";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
	}
	
	public function do_online($point, $total, $service_user_id, $recharge_user_id, $order_id) {
		$pay_money = $total - $point;
		$recharge_param = array(
		    "service_user_id" => $service_user_id,
    	    "recharge_user_id" => $recharge_user_id,
		    "recharge_money" => $total,
		    "pay_money" => $pay_money,
	    	"point" => $point,
    	    "order_id" => $order_id,
		    "pay_type" => 1,
		    "status" => 0
		);
		
		$this->db->insert('recharge_record', $recharge_param);
	    if($this->db->affected_rows()){
		    return $this->db->insert_id();
		}
		else{
			return null;
		}
	}
	
	public function online_callback($order, $transaction_id, $pay_time, $schedule, $router, $commisionSetting) {
		$total = $order['recharge_money'];
		$point = $order['point'];
		$service_user_id = $order['service_user_id'];
		$recharge_user_id = $order['recharge_user_id'];
		$order_id = $order['order_id'];
		$exchange_point = $total * $schedule['give_exchange_rate'] * 0.01;
		$future_point = $total * $schedule['wait_rate']; //待用积分倍率
		$consume_point = $total * $schedule['give_consume_rate'] * 0.01;
		$share_point = $total * $schedule['give_share_rate'] * 0.01;
		$active_timestamp = time() + $schedule['wait_period'] * 24 * 60 * 60;
		$active_time = date('Y-m-d H:i:s', $active_timestamp);
		$pay_money = $order['pay_money'];
		
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
    	    "status" => 1,
    	    "pay_no" => $transaction_id
		);
		
		//生成现金记录
		$paramCash = array(
			"type" => 2,  //1兑换商品 2服务中心充值
		    "money" => $order['pay_money'],
		    "pay_no" => $transaction_id, //转出
	    	"user_id" => $order['service_user_id'],
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
		    "multiple" => $schedule['wait_rate'],   //待用积分倍率
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
        
        $this->db->where('order_id', $order_id);
		$this->db->update('recharge_record', $recharge_param);
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
						    "order_id" => $order_id,
    	    			    "type" => 2
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
    	$recharge_user = $this->member_model->getDataById($recharge_user_id);
		if($recharge_user !== null && $recharge_user['level'] == 0 && $total >= 1000){
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
	
	public function getMonthMoney($id) {
		$begin = date('Y-m').'-01 00:00:00';
		$endTimestamp = strtotime("+1 months", strtotime(date('Y-m')."-01 23:59:59"));
		$end = date('Y-m-d H:i:s');
		$where = array(
			"service_user_id" => $id,
		    "status" => 1
		);
		$and_where = "deleted_at is null AND created_at >= '".$begin."' AND created_at <= '".$end."'";
		
		$query = $this->db->select_sum('recharge_money')
		    ->where($where)
		    ->where($and_where)
		    ->get('recharge_record');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function getTotalMoney($id) {
		$where = array(
			"service_user_id" => $id,
		    "status" => 1
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select_sum('recharge_money')
		    ->where($where)
		    ->where($and_where)
		    ->get('recharge_record');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function record_data_by_page($user_id, $page, $num) {	
		$offset = ($page - 1) * $num;
	    $where = 'deleted_at is null';
	    $and_where = array(
			"service_user_id" => $user_id
		);

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);

		$queryAll = $this->db->get('recharge_record');
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
		
		$query = $this->db->get('recharge_record');
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
	
	public function commision_data_by_page($user_id, $page, $num, $categoty) {	
		$where = array(
			"owner_user_id" => $user_id
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

		$queryAll = $this->db->get('commision_record');
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
		
	    if($categoty != 'total'){
			$begin = date('Y-m', time()).'-01 00:00:00';
			$endTimestamp = strtotime("+1 months", strtotime($begin)) - 1;
			$end = date('Y-m-d H:i:s', $endTimestamp);
			$time_where = "created_at >= '".$begin."' AND created_at <= '".$end."'";
			$this->db->where($time_where);
		}

		$this->db->order_by('id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('commision_record');
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
	
	public function selectPageData($userid, $type, $start, $end, $searchkey, $offset, $num, $status){	
	    $where = 'recharge_record.deleted_at is null';

		//总数
		$this->db->select('count(recharge_record.id) AS total, sum(recharge_record.recharge_money) as recharge, sum(recharge_record.point) as point');
		$this->db->from('recharge_record');
		$this->db->join('member AS service', 'service.id = recharge_record.service_user_id');
		$this->db->join('member AS recharge', 'recharge.id = recharge_record.recharge_user_id');
		
		$this->db->where($where);
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " recharge_record.created_at >= '".$begin."' AND recharge_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}
		
	    if($status != 99){
			$status_where = array(
				"recharge_record.status" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty' && $type != 'empty'){
	    	if($type == 'charge'){
				$user_where = array(
					"recharge_record.recharge_user_id" => $userid
				);
	    	}
	    	else{
				$user_where = array(
					"recharge_record.service_user_id" => $userid
				);
	    	}
			$this->db->where($user_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('recharge_record.order_id', $searchkey);
			$this->db->or_like('service.name', $searchkey);
			$this->db->or_like('recharge.name', $searchkey);
			$this->db->or_like('service.mobile', $searchkey);
			$this->db->or_like('recharge.mobile', $searchkey);
			$this->db->group_end();
		}
		
		
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['total'];
			$recharge = round($data['recharge'], 2);
			$point = $data['point'];
		}
		else{
			$count = 0;
			$recharge = 0;
			$point = 0;
		}
		
		//分页
		$this->db->select('recharge_record.*, service.name AS service_name, recharge.name AS recharge_name, recharge.mobile AS recharge_mobile');
		$this->db->from('recharge_record');
		$this->db->join('member AS service', 'service.id = recharge_record.service_user_id');
		$this->db->join('member AS recharge', 'recharge.id = recharge_record.recharge_user_id');
		
		$this->db->where($where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " recharge_record.created_at >= '".$begin."' AND recharge_record.created_at <= '".$over."'";
			$this->db->where($time_where);
		}
		
	    if($status != 99){
			$status_where = array(
				"recharge_record.status" => $status
			);
			$this->db->where($status_where);
		}
		
	    if($userid != 'empty' && $type != 'empty'){
	    	if($type == 'charge'){
				$user_where = array(
					"recharge_record.recharge_user_id" => $userid
				);
	    	}
	    	else{
				$user_where = array(
					"recharge_record.service_user_id" => $userid
				);
	    	}
			$this->db->where($user_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('recharge_record.order_id', $searchkey);
			$this->db->or_like('service.name', $searchkey);
			$this->db->or_like('recharge.name', $searchkey);
			$this->db->or_like('service.mobile', $searchkey);
			$this->db->or_like('recharge.mobile', $searchkey);
			$this->db->group_end();
		}
		$this->db->order_by('recharge_record.id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get();
	    if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array(
		        "count" => $count,
			    "recharge" => $recharge,
			    "point" => $point,
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
		$this->db->update('recharge_record', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
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
	
	public function selectRechargeDataByOrderid($order_id) {
		$where = array(
			"order_id" => $order_id
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('recharge_record');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function selectNotPayCompleteOrder() {
		//3分钟以前的订单
		$compareTime = date('Y-m-d H:i:s', time() - 3*60);
		$where = array(
			"status" => 0,
		);
		$and_where = "deleted_at is null AND pay_money > 0 AND created_at < '".$compareTime."'";
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('recharge_record');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function bindWechatInfo($userInfo, $data) {
		$param = array(
		    'service_openid'=>$data['openid'],
			'sex'=>$data['sex'],
			'province'=>$data['province'],
			'city'=>$data['city'],
			'country'=>$data['country'],
		    'nickname'=>$data['nickname'],
			'headimgurl'=>$data['headimgurl'],
		    'service_bind_time' => date('Y-m-d H:i:s', time()),
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
		    'service_openid'=>$data['openid'],
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
	
	public function selectDataByOrderid($order_id) {
		$where = array(
			"order_id" => $order_id
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('recharge_record');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	/*
	 * 2小时未支付自动关闭报单
	 */
	public function selectNotPayOrder() {
		$compareTime = date('Y-m-d H:i:s', time() - 2*60*60);
		$where = array(
			"status" => 0
		);
		$and_where = "deleted_at is null AND created_at < '".$compareTime."'";
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('recharge_record');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function closeDeclaration($id) {
		$param = array(
		    "status" => 2,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $id);
		$this->db->update('recharge_record', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function do_online_wallet($point, $total, $service_user_id, $recharge_user_id, $order_id, $transaction_id, $schedule, $router, $commisionSetting) {
		$exchange_point = $total * $schedule['give_exchange_rate'] * 0.01;
		$future_point = $total * $schedule['wait_rate'];
		$consume_point = $total * $schedule['give_consume_rate'] * 0.01;
		$share_point = $total * $schedule['give_share_rate'] * 0.01;
		$active_timestamp = time() + $schedule['wait_period'] * 24 * 60 * 60;
		$active_time = date('Y-m-d H:i:s', $active_timestamp);
		$pay_money = $total - $point;
		
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
		    "pay_type" => 2,
	    	"point" => $point,
    	    "order_id" => $order_id,
		    "status" => 1,
		    "pay_no" => $transaction_id
		);
		
		//生成现金记录
		$paramCash = array(
			"type" => 2,  //1兑换商品 2服务中心充值
		    "money" => $pay_money,
		    "pay_no" => $transaction_id, //转出
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
		
		$serviceMemberSql = "UPDATE member SET commision = commision - ".$pay_money.", consume_point = consume_point - ".$point." WHERE id = ".$service_user_id;
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
		
		$wallet_param = array(
    	    "type" => 1,  //1报单  2兑换商品
		    "money" => $pay_money,
	    	"user_id" => $service_user_id,
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
        $this->db->insert('wallet_record', $wallet_param);
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
	    	    		//上面二层会员
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
    	$recharge_user = $this->member_model->getDataById($recharge_user_id);
		if($recharge_user !== null && $recharge_user['level'] == 0 && $total >= 1000){
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
}
	