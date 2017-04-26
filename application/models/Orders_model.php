<?php
class Orders_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function createByHavePay($sku_id, $num, $goods, $total_point_price, $total_cash_price, $logisticFee, $comment, $user_id, $order_id, $address) {
		//事务
        $this->db->trans_begin();
        
	    //先更新库存销量，防止超卖
		$flag = false;
	    if($sku_id){
	    	//规格商品
			$goodSql = "UPDATE goods_sku SET total = total - ".$num.", updated_at = '".date('Y-m-d H:i:s', time())."' WHERE id = ".$sku_id." AND total >= ".$num;
		}
		else{
		    $goodSql = "UPDATE goods SET total = total - ".$num.", sales_volume = sales_volume + ".$num." WHERE id = ".$goods['id']." AND total >= ".$num;
		}
		
	    $this->db->query($goodSql);
	    if($this->db->affected_rows()){
			$flag = true;
			
	        if($sku_id){
				//规格商品更新销量
				$volume_sql = "UPDATE goods SET total = total - ".$num.", sales_volume = sales_volume + ".$num." WHERE id = ".$goods['id'];
				$this->db->query($volume_sql);
			}

			//消费积分加现金购买
			//订单中保留商品图片等信息  防止以后更改商品信息 导致订单找不到对应的信息
			$param = array(
				'goods_id' => $goods['id'],
				'goods_name' => $goods['name'],
				'goods_face' => $goods['face'],
				'goods_model' => $goods['model'],
			    'sku_id' => $sku_id,
				'num' => $num,
				'point_price' => $goods['point_price'],
				'cash_price' => $goods['cash_price'],
				'total_point_price' => $total_point_price,
				'total_cash_price' => $total_cash_price,
			    'logistic_fee' => $logisticFee,
			    'memo' => $comment,
			    'user_id' => $user_id,
			    'order_id' => $order_id,
			    'order_address' => $address,
			    'status' => 0
			);
			
			$paramMember = array(
				'address' => $address
	        );
	        
			$this->db->insert('orders', $param);
			$this->db->where('id', $user_id);
			$this->db->update('member', $paramMember);
	    }
	    
	    if ($this->db->trans_status() === FALSE || !$flag)
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
	
	public function createByNoPay($sku_id, $num, $goods, $total_point_price, $total_cash_price, $comment, $user_id, $order_id, $address) {
		//事务
        $this->db->trans_begin();
        
	    //先更新库存销量，防止超卖
		$flag = false;
	    if($sku_id){
	    	//规格商品
			$goodSql = "UPDATE goods_sku SET total = total - ".$num.", updated_at = '".date('Y-m-d H:i:s', time())."' WHERE id = ".$sku_id." AND total >= ".$num;
		}
		else{
		    $goodSql = "UPDATE goods SET total = total - ".$num.", sales_volume = sales_volume + ".$num." WHERE id = ".$goods['id']." AND total >= ".$num;
		}
		
	    $this->db->query($goodSql);
	    if($this->db->affected_rows()){
			$flag = true;
			
	        if($sku_id){
				//规格商品更新销量
				$volume_sql = "UPDATE goods SET total = total - ".$num.", sales_volume = sales_volume + ".$num." WHERE id = ".$goods['id'];
				$this->db->query($volume_sql);
			}
		
			//纯积分兑换
			//订单中保留商品图片等信息  防止以后更改商品信息 导致订单找不到对应的信息
			$param = array(
				'goods_id' => $goods['id'],
				'goods_name' => $goods['name'],
				'goods_face' => $goods['face'],
				'goods_model' => $goods['model'],
			    'sku_id' => $sku_id,
				'num' => $num,
				'point_price' => $goods['point_price'],
				'cash_price' => $goods['cash_price'],
				'total_point_price' => $total_point_price,
				'total_cash_price' => $total_cash_price,
			    'memo' => $comment,
			    'user_id' => $user_id,
			    'order_id' => $order_id,
			    'order_address' => $address,
			    'status' => 1
			);
			
			if($goods['model'] == 1){
				//生成购物券记录
				$paramPoint = array(
					"type" => 2, //1充值  2兑换商品
				    "point" => $total_point_price,
			    	"user_id" => $user_id,
		    	    "order_id" => $order_id
				);
				$this->db->insert('exchange_record', $paramPoint);
				$memberSql = "UPDATE member SET exchange_point = exchange_point - ".$total_point_price.", address = '".$address."' WHERE id = ".$user_id;
				$this->db->query($memberSql);
			}
			elseif($goods['model'] == 2){
				//生成消费积分记录
				$paramPoint = array(
					"type" => 4,  //可用积分转消费积分 2积分转让 3下线充值返利 4兑换商品
				    "point" => $total_point_price,
				    "reason" => 1, //转出
			    	"user_id" => $user_id,
				    "order_id" => $order_id
				);
				$this->db->insert('consume_record', $paramPoint);
				$memberSql = "UPDATE member SET consume_point = consume_point - ".$total_point_price.", address = '".$address."' WHERE id = ".$user_id;
				$this->db->query($memberSql);
			}
		    elseif($goods['model'] == 3){
				return false;
			}
			else{
				return false;
			}
	
			$this->db->insert('orders', $param);
	    }
		
	    if ($this->db->trans_status() === FALSE || !$flag)
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
	
	public function order_data_by_page($user_id, $searchkey, $page, $num) {	
		$offset = ($page - 1) * $num;
	    $where = 'deleted_at is null';
	    $and_where = array(
			"user_id" => $user_id
		);

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		$this->db->where($and_where);
		
		if($searchkey){
			$this->db->group_start();
		    $this->db->like('goods_name', $searchkey);
		    $this->db->or_like('order_id', $searchkey);
		    $this->db->group_end();
		}
		
		$queryAll = $this->db->get('orders');
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
	
		if($searchkey){
			$this->db->group_start();
		    $this->db->like('goods_name', $searchkey);
		    $this->db->or_like('order_id', $searchkey);
		    $this->db->group_end();
		}
		$this->db->order_by('id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('orders');
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
		    ->get('orders');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
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
		    ->get('orders');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function selectPageData($status, $start, $end, $searchkey, $offset, $num, $comid, $type){	
	    $where = 'orders.deleted_at is null';

		//总数
		$this->db->select('count(orders.id) AS total');
		$this->db->from('orders');
		$this->db->join('member', 'member.id = orders.user_id', 'left');
		$this->db->join('goods', 'goods.id = orders.goods_id');
	    if($comid != 'empty'){
	    	//商家id
			$com_where = array(
				"goods.company_id" => $comid
			);
			$this->db->where($com_where);
		}
		
		if($status != 99){
			$status_where = array(
				"orders.status" => $status
			);
			$this->db->where($status_where);
		}
		$this->db->where($where);
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	if($type == 'merchant'){
	    	    $time_where = " orders.complete_time >= '".$begin."' AND orders.complete_time <= '".$over."'";
	    	}
	    	else{
	    		$time_where = " orders.created_at >= '".$begin."' AND orders.created_at <= '".$over."'";
	    	}
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('orders.order_id', $searchkey);
			$this->db->or_like('orders.goods_name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('orders.order_address', $searchkey);
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
		$this->db->select('orders.*, member.name, member.mobile, goods.company_id');
		$this->db->from('orders');
		$this->db->join('member', 'member.id = orders.user_id', 'left');
		$this->db->join('goods', 'goods.id = orders.goods_id');
	    if($comid != 'empty'){
	    	//商家id
			$com_where = array(
				"goods.company_id" => $comid
			);
			$this->db->where($com_where);
		}
		
	    if($status != 99){
			$status_where = array(
				"orders.status" => $status
			);
			$this->db->where($status_where);
		}
		$this->db->where($where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	if($type == 'merchant'){
	    	    $time_where = " orders.complete_time >= '".$begin."' AND orders.complete_time <= '".$over."'";
	    	}
	    	else{
	    		$time_where = " orders.created_at >= '".$begin."' AND orders.created_at <= '".$over."'";
	    	}
			$this->db->where($time_where);
		}

	    if($searchkey){
	    	$this->db->group_start();
			$this->db->like('orders.order_id', $searchkey);
			$this->db->or_like('orders.goods_name', $searchkey);
			$this->db->or_like('member.mobile', $searchkey);
			$this->db->or_like('member.name', $searchkey);
			$this->db->or_like('orders.order_address', $searchkey);
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
	
	public function deleteBatch($idlist) {
		$param = array(
		    'deleted_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where_in('id', $idlist);
		$this->db->update('orders', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function changeStatus($id, $status) {
		//事务
		$data = $this->selectDataById($id);
		if($data !== null){
		    $this->db->trans_begin();
	        
	        $param = array(
	            "last_status" => $data['status'],
			    "status" => $status,
			    "updated_at" => date('Y-m-d H:i:s', time())
			);
			
			if($status == 2){
				$extra_time = array(
				    "logistic_time" => date('Y-m-d H:i:s', time())
				);
				$param = array_merge($param, $extra_time);
			}
			elseif($status == 3){
				$extra_time = array(
				    "complete_time" => date('Y-m-d H:i:s', time())
				);
				$param = array_merge($param, $extra_time);
			}
			
			$this->db->where('id', $id);
			$this->db->update('orders', $param);
	        
			/*
			 * 待发货的订单或者已发货的订单 关闭或退款，需要退回用户所用掉的积分
			 * 此处不涉及现金
			 * 有现金则微信原路退回，记录等信息在回调时处理
			 */
			if(($data['status'] == 1 || $data['status'] == 2 || $data['status'] == 7) && ($status == 4 || $status == 6) && $data['total_cash_price'] == 0 && $data['share_id'] == 0){
				if($data['goods_model'] == 1){
					//购物券记录
					$exchange_param = array(
					    "type" => 4, //1充值  2兑换商品 3升级  4退货
					    "point" => $data['total_point_price'],
				    	"user_id" => $data['user_id'],
			    	    "order_id" => $data['order_id']
					);
					
					//购物券
					$memberSql = "UPDATE member SET exchange_point = exchange_point + ".$data['total_point_price']." WHERE id = ".$data['user_id'];
					$this->db->query($memberSql);
					
					//增加兑换积分记录
					$this->db->insert('exchange_record', $exchange_param);
				}
				elseif($data['goods_model'] == 2 || $data['goods_model'] == 3){
					$consume_param = array(
			    	    "type" => 8, //1可用积分转消费积分 2积分转让 3充值报单 4兑换商品 5升级服务中心 6分享 7报单抵扣 8退货
			    	    "reason" => 2,  //1转出  2转入
					    "point" => $data['total_point_price'],
				    	"user_id" => $data['user_id'],
					    "order_id" => $data['order_id']
					);
					
					//消费积分
					$memberSql = "UPDATE member SET consume_point = consume_point + ".$data['total_point_price']." WHERE id = ".$data['user_id'];
					$this->db->query($memberSql);
					
					//增加消费积分记录
					$this->db->insert('consume_record', $consume_param);
				}
			}
			
		    if($status == 4){
				//订单关闭时，修改销量和库存，此处不涉及退款
				if($data['sku_id']){
					//有规格商品
					$goodsSql = "UPDATE goods_sku SET total = total + ".$data['num'].", updated_at = '".date('Y-m-d H:i:s', time())."' WHERE id = ".$data['sku_id'];
					$volumeSql = "UPDATE goods SET total = total + ".$data['num'].", sales_volume = sales_volume - ".$data['num']." WHERE id = ".$data['goods_id'];
					$this->db->query($volumeSql);
				}
				else{
				    $goodsSql = "UPDATE goods SET total = total + ".$data['num'].", sales_volume = sales_volume - ".$data['num']." WHERE id = ".$data['goods_id'];
				}
				$this->db->query($goodsSql);
				
				if($data['share_id'] != 0 && $data['share_uid'] != 0){
					//退回消费积分给分享者   未付款订单关闭时
					$total_point_price = $data['point_price'] * $data['num'];
					$consume_param = array(
			    	    "type" => 8, //1可用积分转消费积分 2积分转让 3充值报单 4兑换商品 5升级服务中心 6分享 7报单抵扣 8退货
			    	    "reason" => 2,  //1转出  2转入
					    "point" => $total_point_price,
				    	"user_id" => $data['share_uid'],
					    "order_id" => $data['order_id']
					);
				}
			}
			
			//确认订单
			if($status == 3){
				//商家商品，下单送商家可用积分，送商家上级消费积分
				$this->load->model('goods_model');
				$goods = $this->goods_model->selectDataById($data['goods_id']);
				if($goods !== null && $goods['company_useable_point'] > 0 && $goods['company_id'] > 0 && $goods['company_get_type'] > 1){
					$useable_point = $goods['company_useable_point'] * $data['num'];
					if($goods['company_get_type'] == 2){
						//可用积分
						$useable_param = array(
				    	    "type" => 4,   //1复投 2可用转消费 3复投结算 4订单结算 5商家结算
						    "point" => $useable_point,
					    	"user_id" => $goods['company_id'],
						    "order_id" => $data['order_id']
						);
						$this->db->insert('useable_record', $useable_param);
						$companySelfSql = "UPDATE member SET useable_point = useable_point + ".$useable_point." WHERE id = ".$goods['company_id'];
					    $this->db->query($companySelfSql);
					}
					elseif($goods['company_get_type'] == 3){
						//消费积分
						$com_consume_param = array(
				    	    "type" => 12, //1可用积分转消费积分  2积分转让  3充值报单  4兑换商品   5升级服务中心  6分享  7报单抵扣 8退款  9推荐商家  10兑充升级 11商家结算 12订单结算
				    	    "reason" => 2,  //1转出  2转入
						    "point" => $useable_point,
					    	"user_id" => $goods['company_id'],
						    "order_id" => $data['order_id']
						);
						$this->db->insert('consume_record', $com_consume_param);
						$companySelfSql = "UPDATE member SET consume_point = consume_point + ".$useable_point." WHERE id = ".$goods['company_id'];
					    $this->db->query($companySelfSql);
					}
				 	
					$this->load->model('member_model');			
					$company = $this->member_model->getDataById($goods['company_id']);
					if($company !== null){
						$parent_consume_point = round($goods['company_useable_point'] * $data['num'] * 0.02);
						$parent_consume_param = array(
				    	    "type" => 9,  //1可用积分转消费积分  2积分转让  3充值报单  4兑换商品   5升级服务中心  6分享  7报单抵扣 8退款 9推荐商家
						    "point" => $parent_consume_point,
						    "reason" => 2, //1转出  2转入
					    	"user_id" => $company['parent_id'],
						    "order_id" => $data['order_id']
						);
						$this->db->insert('consume_record', $parent_consume_param);
						$companyParentSql = "UPDATE member SET consume_point = consume_point + ".$parent_consume_point." WHERE id = ".$company['parent_id'];
						$this->db->query($companyParentSql);
					}
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
	
	public function changeOrderPayStatus($order_id, $status, $pay_no, $pay_time) {
		$is_new = false;
		$order = $this->selectDataByOrderid($order_id);
		if($order !== null && $order['status'] == 0){
			if($order['share_id'] != 0 && $order['share_uid'] != 0 && $order['order_address']){
				//分享商品
				$this->load->model('member_model');
    			//取省份前两个字比较， 一个UTF-8的中文字符，会把它当做长度为3来处理
	    		$addressObj = json_decode($order['order_address'], true);
	    		$province = substr($addressObj['provinceName'], 0, 6);
	    		$addressObj['detailInfo'] = str_replace(array("\r\n", "\r", "\n"), '', $addressObj['detailInfo']);
	    		$order['order_address'] = json_encode($addressObj);
	    		$userInfo = $this->member_model->getDataByMobile($addressObj['telNumber']);
	    		if($userInfo === null){
	    		    $recommend_user = $this->member_model->getDataById($order['share_uid']);
	    		}
			}
			
			//事务
	        $this->db->trans_begin();
	        if($order['share_id'] != 0 && $order['share_uid'] != 0 && $order['order_address']){
	        	//分享商品
	            if($userInfo === null){
	    			//注册一个用户
	    			$my_invest_code = $this->random_str(6);
	    			$password = $this->random_num(6);
	    			$param_regist = array(
					    "mobile" => $addressObj['telNumber'],
						"password" => $this->password($password),
					    "code" => $my_invest_code,
	    			    "parent_mobile"=> $recommend_user['mobile'],
	    			    "parent_id"=> $recommend_user['id'],
	    			    'address' => $order['order_address']
					);
					$this->db->insert('member', $param_regist);
					$user_id = $this->db->insert_id();
					$router = $recommend_user['router']. "," . $user_id;
					$update_param = array(
					    "router"=>$router
					);
					//更新member表的router数据
			        $this->db->where('id', $user_id);
			        $this->db->update('member', $update_param);
			        $is_new = true;
	    		}
	    		else{
	    			//更新用户地址信息
	    			$user_id = $userInfo['id'];
	    			$update_param = array(
					    'address' => $order['order_address']
					);
					$this->db->where('id', $user_id);
			        $this->db->update('member', $update_param);
	    		}
	        }
	        
			$param = array(
			    "status" => $status,
				"pay_no" => $pay_no,
				"pay_time" => $pay_time,
			    'updated_at' => date('Y-m-d H:i:s', time())
			);
			
			if($order['share_id'] != 0 && $order['share_uid'] != 0 && $user_id){
				//更新订单用户ID,分享商品微信支付生成订单时没加user_id
				$uid_arr = array(
				    "user_id" => $user_id,
				);
				$param = array_merge($param, $uid_arr);
				$order['user_id'] = $user_id;
			}
			
			if($order['share_id'] == 0){
				if($order['goods_model'] == 1){
					//生成购物券记录
					$paramPoint = array(
						"type" => 2, //1充值  2兑换商品
					    "point" => $order['total_point_price'],
				    	"user_id" => $order['user_id'],
			    	    "order_id" => $order_id
					);
					$this->db->insert('exchange_record', $paramPoint);
					$memberSql = "UPDATE member SET exchange_point = exchange_point - ".$order['total_point_price']." WHERE id = ".$order['user_id'];
					$this->db->query($memberSql);
				}
				elseif($order['goods_model'] == 2 || $order['goods_model'] == 3){
					//生成消费积分记录
					$paramPoint = array(
						"type" => 4,  //可用积分转消费积分 2积分转让 3下线充值返利 4兑换商品
					    "point" => $order['total_point_price'],
					    "reason" => 1, //转出
				    	"user_id" => $order['user_id'],
					    "order_id" => $order_id
					);
					$this->db->insert('consume_record', $paramPoint);
					$memberSql = "UPDATE member SET consume_point = consume_point - ".$order['total_point_price']." WHERE id = ".$order['user_id'];
					$this->db->query($memberSql);
				}
				else{
					return false;
				}
			}
			
			
			//生成现金记录
			$paramCash = array(
				"type" => 1,  //1兑换商品 2服务中心充值
			    "money" => $order['total_cash_price'],
			    "pay_no" => $pay_no, //转出
		    	"user_id" => $order['user_id'],
			    "order_id" => $order_id
			);
			
			$this->db->insert('cash_record', $paramCash);
				
			$this->db->where('order_id', $order_id);
			$this->db->update('orders', $param);
			
		    if ($this->db->trans_status() === FALSE)
			{
				// 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
				$this->db->trans_rollback();
				return false;
			}
			else
			{
				$this->db->trans_commit();
				if($is_new){
					$type = 11;
					$tpl = $this->getSmsTemplateByType($type);
		            if($tpl !== null && $tpl['status'] == 1){
		            	$message = $tpl['content'];
			            $message = str_replace('{mobile}', $addressObj['telNumber'] ,$message);
			            $message = str_replace('{password}', $password ,$message);
			            $this->send($type, $message, $addressObj['telNumber']);
				    }
				}
			    return true;
			}
		}
		else{
			return false;
		}
	}
	
	public function selectNotPayCompleteOrder() {
		//3分钟以前的订单
		$compareTime = date('Y-m-d H:i:s', time() - 3*60);
		$where = array(
			"status" => 0,
		    "goods_model" => 3
		);
		$and_where = "deleted_at is null AND total_cash_price > 0 AND created_at < '".$compareTime."'";
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('orders');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function submitLogisticsInfo($id, $order_address) {
		$param = array(
		    "status" => 2,
            "logistic_time" => date('Y-m-d H:i:s', time()),
		    "order_address" => $order_address,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$status = 1;
		$this->db->where('id', $id);
		$this->db->where('status', $status);
		$this->db->update('orders', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function modifyLogisticsInfo($id, $order_address) {
		$param = array(
		    "order_address" => $order_address,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $id);
		$this->db->update('orders', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	/*
	 * 发货15天自动确认订单
	 */
	public function selectNotSureOrder() {
		$compareTime = date('Y-m-d H:i:s', time() - 7*24*60*60);
		$where = array(
			"status" => 2
		);
		$and_where = "deleted_at is null AND updated_at < '".$compareTime."'";
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('orders');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	/*
	 * 2小时未支付自动关闭订单
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
		    ->get('orders');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	/*
	 * 退款订单
	 */
    public function selectRefundOrder() {
		$compareTime = date('Y-m-d H:i:s', time() - 20*60);
		$where = array(
			"status" => 5  //退款
		);
		$and_where = "deleted_at is null AND created_at < '".$compareTime."'";
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('orders');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	/*
	 *涉及现金
	 */
	public function processRefundOrder($data, $callback) {
		//退款成功,关闭订单
		//事务
        $this->db->trans_begin();
        
        $param = array(
            "last_status" => $data['status'],
		    "status" => 6,  //已退款
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('orders', $param);
		
		//修改销量和库存
		if($data['sku_id']){
			//有规格商品
			$goodsSql = "UPDATE goods_sku SET total = total + ".$data['num'].", updated_at = '".date('Y-m-d H:i:s', time())."' WHERE id = ".$data['sku_id'];
			$volumeSql = "UPDATE goods SET total = total + ".$data['num'].", sales_volume = sales_volume - ".$data['num']." WHERE id = ".$data['goods_id'];
			$this->db->query($volumeSql);
		}
		else{
		    $goodsSql = "UPDATE goods SET total = total + ".$data['num'].", sales_volume = sales_volume - ".$data['num']." WHERE id = ".$data['goods_id'];
		}
		$this->db->query($goodsSql);
        
		if($data['status'] == 5 && $data['total_cash_price'] > 0){
			//退回用户所用掉的积分及现金
			$money = $callback['total_fee']/100;
			//生成现金记录
			$paramCash = array(
				"type" => 4,  //1购买商品  2充值   3升级服务中心  4退款
			    "money" => $money,
			    "pay_no" => $callback['transaction_id'], //转出
		    	"user_id" => $data['user_id'],
			    "order_id" => $data['order_id']
			);
			//增加现金记录
			$this->db->insert('cash_record', $paramCash);

			if($data['share_id'] == 0){
				//非分享商品
				if($data['goods_model'] == 1){
					//购物券记录
					$exchange_param = array(
					    "type" => 4, //1充值  2兑换商品 3升级  4退货
					    "point" => $data['total_point_price'],
				    	"user_id" => $data['user_id'],
			    	    "order_id" => $data['order_id']
					);
					
					$memberSql = "UPDATE member SET exchange_point = exchange_point + ".$data['total_point_price']." WHERE id = ".$data['user_id'];
					$this->db->query($memberSql);
					
					//增加兑换积分记录
					$this->db->insert('exchange_record', $exchange_param);
				}
				elseif($data['goods_model'] == 2 || $data['goods_model'] == 3){
					$consume_param = array(
			    	    "type" => 8, //1可用积分转消费积分 2积分转让 3充值报单 4兑换商品 5升级服务中心 6分享 7报单抵扣 8退货
			    	    "reason" => 2,  //1转出  2转入
					    "point" => $data['total_point_price'],
				    	"user_id" => $data['user_id'],
					    "order_id" => $data['order_id']
					);
					
					$memberSql = "UPDATE member SET consume_point = consume_point + ".$data['total_point_price']." WHERE id = ".$data['user_id'];
					$this->db->query($memberSql);
					
					//增加消费积分记录
					$this->db->insert('consume_record', $consume_param);
				}
			}
			elseif($data['share_uid'] != 0){
				//分享商品
				//退回消费积分给分享者
				$total_point_price = $data['point_price'] * $data['num'];
				$consume_param = array(
		    	    "type" => 8, //1可用积分转消费积分 2积分转让 3充值报单 4兑换商品 5升级服务中心 6分享 7报单抵扣 8退货
		    	    "reason" => 2,  //1转出  2转入
				    "point" => $total_point_price,
			    	"user_id" => $data['share_uid'],
				    "order_id" => $data['order_id']
				);
				
				$shareSql = "UPDATE member SET consume_point = consume_point + ".$total_point_price." WHERE id = ".$data['share_uid'];
				$this->db->query($shareSql);
				
				//增加消费积分记录
				$this->db->insert('consume_record', $consume_param);
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
	
	public function createByHavePayWallet($sku_id, $num, $goods, $total_point_price, $total_cash_price, $logisticFee, $comment, $user_id, $order_id, $address, $transaction_id) {
		//事务
        $this->db->trans_begin();
        
	    //先更新库存销量，防止超卖
		$flag = false;
		if($sku_id){
			//规格商品
			$goodSql = "UPDATE goods_sku SET total = total - ".$num.", updated_at = '".date('Y-m-d H:i:s', time())."' WHERE id = ".$sku_id." AND total >= ".$num;
		}
		else{
		    $goodSql = "UPDATE goods SET total = total - ".$num.", sales_volume = sales_volume + ".$num." WHERE id = ".$goods['id']." AND total >= ".$num;
		}
		
		$this->db->query($goodSql);
		if($this->db->affected_rows()){
			$flag = true;
			
			if($sku_id){
				//规格商品更新销量
				$volume_sql = "UPDATE goods SET total = total - ".$num.", sales_volume = sales_volume + ".$num." WHERE id = ".$goods['id'];
				$this->db->query($volume_sql);
			}
		
			//纯积分兑换
			//订单中保留商品图片等信息  防止以后更改商品信息 导致订单找不到对应的信息
			$param = array(
				'goods_id' => $goods['id'],
				'goods_name' => $goods['name'],
				'goods_face' => $goods['face'],
				'goods_model' => $goods['model'],
			    'sku_id' => $sku_id,
				'num' => $num,
				'point_price' => $goods['point_price'],
				'cash_price' => $goods['cash_price'],
				'total_point_price' => $total_point_price,
				'total_cash_price' => $total_cash_price,
			    'logistic_fee' => $logisticFee,
			    'memo' => $comment,
			    'user_id' => $user_id,
			    'order_id' => $order_id,
			    'order_address' => $address,
			    "pay_no" => $transaction_id,
			    "pay_time" => date('Y-m-d H:i:s', time()),
			    'status' => 1
			);
			
			//生成现金记录
			$paramCash = array(
				"type" => 1,  //1兑换商品 2服务中心充值
			    "money" => $total_cash_price,
			    "pay_no" => $transaction_id,
		    	"user_id" => $user_id,
			    "order_id" => $order_id
			);
			
			$wallet_param = array(
	    	    "type" => 2,  //1报单  2兑换商品
			    "money" => $total_cash_price,
		    	"user_id" => $user_id,
			    "order_id" => $order_id
			);
	
	        $this->db->insert('wallet_record', $wallet_param);
			$this->db->insert('cash_record', $paramCash);
		    if($goods['model'] == 1){
				//生成购物券记录
				$paramPoint = array(
					"type" => 2, //1充值  2兑换商品
				    "point" => $total_point_price,
			    	"user_id" => $user_id,
		    	    "order_id" => $order_id
				);
				$this->db->insert('exchange_record', $paramPoint);
				$memberSql = "UPDATE member SET commision = commision - ".$total_cash_price.", exchange_point = exchange_point - ".$total_point_price.", address = '".$address."' WHERE id = ".$user_id;
				$this->db->query($memberSql);
			}
			elseif($goods['model'] == 2 || $goods['model'] == 3){
				//生成消费积分记录
				$paramPoint = array(
					"type" => 4,  //可用积分转消费积分 2积分转让 3下线充值返利 4兑换商品
				    "point" => $total_point_price,
				    "reason" => 1, //转出
			    	"user_id" => $user_id,
				    "order_id" => $order_id
				);
				$this->db->insert('consume_record', $paramPoint);
				$memberSql = "UPDATE member SET commision = commision - ".$total_cash_price.", consume_point = consume_point - ".$total_point_price.", address = '".$address."' WHERE id = ".$user_id;
				$this->db->query($memberSql);
			}
			else{
				return false;
			}
			
			$this->db->insert('orders', $param);
		}
		
	    if ($this->db->trans_status() === FALSE || !$flag)
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
	
	/*
	 *涉及现金，钱包支付退款
	 */
	public function walletRefundOrder($data) {
		//退款成功,关闭订单
		//事务
        $this->db->trans_begin();
        
        $param = array(
            "last_status" => $data['status'],
		    "status" => 6,  //已退款
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('orders', $param);
		
		//修改销量和库存
		if($data['sku_id']){
			//有规格商品
			$goodsSql = "UPDATE goods_sku SET total = total + ".$data['num'].", updated_at = '".date('Y-m-d H:i:s', time())."' WHERE id = ".$data['sku_id'];
			$volumeSql = "UPDATE goods SET total = total + ".$data['num'].", sales_volume = sales_volume - ".$data['num']." WHERE id = ".$data['goods_id'];
			$this->db->query($volumeSql);
		}
		else{
		    $goodsSql = "UPDATE goods SET total = total + ".$data['num'].", sales_volume = sales_volume - ".$data['num']." WHERE id = ".$data['goods_id'];
		}
		$this->db->query($goodsSql);
        
		if($data['total_cash_price'] > 0){
			//退回用户所用掉的积分及现金
			$money = $data['total_cash_price'];
			//生成现金记录
			$paramCash = array(
				"type" => 4,  //1购买商品  2充值   3升级服务中心  4退款
			    "money" => $money,
			    "pay_no" => $data['pay_no'], //转出
		    	"user_id" => $data['user_id'],
			    "order_id" => $data['order_id']
			);
			//增加现金记录
			$this->db->insert('cash_record', $paramCash);
			
			//生成钱包退回记录
			$paramWallet = array(
				"type" => 3,  //1报单 2购买商品  3退款
			    "money" => $money,
		    	"user_id" => $data['user_id'],
			    "order_id" => $data['order_id']
			);
			//增加钱包退回记录
			$this->db->insert('wallet_record', $paramWallet);

			if($data['goods_model'] == 1){
				//购物券记录
				$exchange_param = array(
				    "type" => 4, //1充值  2兑换商品 3升级  4退货
				    "point" => $data['total_point_price'],
			    	"user_id" => $data['user_id'],
		    	    "order_id" => $data['order_id']
				);
				
				$memberSql = "UPDATE member SET commision = commision + ".$money.", exchange_point = exchange_point + ".$data['total_point_price']." WHERE id = ".$data['user_id'];
				$this->db->query($memberSql);
				
				//增加兑换积分记录
				$this->db->insert('exchange_record', $exchange_param);
			}
			elseif($data['goods_model'] == 2 || $data['goods_model'] == 3){
				$consume_param = array(
		    	    "type" => 8, //1可用积分转消费积分 2积分转让 3充值报单 4兑换商品 5升级服务中心 6分享 7报单抵扣 8退货
		    	    "reason" => 2,  //1转出  2转入
				    "point" => $data['total_point_price'],
			    	"user_id" => $data['user_id'],
				    "order_id" => $data['order_id']
				);
				
				$memberSql = "UPDATE member SET commision = commision + ".$money.", consume_point = consume_point + ".$data['total_point_price']." WHERE id = ".$data['user_id'];
				$this->db->query($memberSql);
				
				//增加消费积分记录
				$this->db->insert('consume_record', $consume_param);
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
	
	/*
	 * 自动结算确认订单的分享佣金
	 */
	public function selectNotClearShare() {
		$where = array(
			"status" => 3,
		    "is_clear_share" => 0
		);
		$and_where = "deleted_at is null AND share_id != 0 AND share_uid != 0";
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('orders');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
    public function clearShare($order, $goods, $share_user) {
    	$this->db->trans_begin();
		$param = array(
			'is_clear_share' => 1,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
	
		$this->db->where('id', $order['id']);
		$this->db->update('orders', $param);
		
		$total = $goods['cash_price'] * $order['num'];
		$pay_money = $order['cash_price'] * $order['num'];
		if($total >= $pay_money){
		    $commision = $total - $pay_money;
		}
		else{
			$commision = $pay_money - $total;
		}
		
		$commision_param = array(
    	    "level" => $share_user['level'],
		    "owner_user_id" => $share_user['id'],
	    	"recharge_user_id" => $order['user_id'],
            "order_money" => $total,
            "pay_money" => $pay_money,
		    "order_id" => $order['order_id'],
		    "commision" => $commision,
            "type" => 3 //3商品分享
		);
		$this->db->insert('commision_record', $commision_param);
		
		$memberSql = "UPDATE member SET commision = commision + ".$commision." WHERE id = ".$share_user['id'];
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
    
    public function random_num($length)
    {
        //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
        $arr = array_merge(range(0, 9));

        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++)
        {
            $rand = mt_rand(0, $arr_len-1);
            $str.=$arr[$rand];
        }

        return $str;
    }
    
    public function password($original){
		$string = "goodluck";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
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
	
	private function send($type, $message, $mobile){
		$this->load->model('sms_model');
		$insert_id = $this->sms_model->insert($mobile, $message, $type);
		if($insert_id){
			$resJson = $this->sms_model->send($mobile, $message);
			$res = json_decode($resJson, true);
			if($res && $res['returnstatus'] && strtolower($res['returnstatus']) == 'success'){
				//修改发送状态
				$updateInfo = $this->sms_model->update($insert_id);
        	    $return = array("code"=>0, "message"=>"发送成功！");
			}
		    else{
		    	$return = array("code"=>10001, "message"=>"发送失败！");
		    }
		}
		else{
        	$return = array("code"=>10002, "message"=>"插入数据失败！");
        }
        
        return $return;
	}
}
	