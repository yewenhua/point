<?php
class Share_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function createShareByHavePay($sku_id, $share, $num, $goods, $total_cash_price, $logisticFee, $comment, $order_id, $address) {
		//事务
        $this->db->trans_begin();
        
	    //先更新库存销量，防止超卖
		$flag = false;
		$total_point_price = 0;
	    $shareSql = "UPDATE share_goods SET sales_num = sales_num + ".$num.", rest_num = rest_num - ".$num.", updated_at = '".date('Y-m-d H:i:s', time())."' WHERE id = ".$share['id']." AND rest_num >= ".$num;
		$this->db->query($shareSql);
	    if($this->db->affected_rows()){
	        if($sku_id){
				//规格商品更新销量
				$volume_sql = "UPDATE goods SET total = total - ".$num.", sales_volume = sales_volume + ".$num." WHERE id = ".$goods['id'];
				$this->db->query($volume_sql);
				
				$goodSql = "UPDATE goods_sku SET total = total - ".$num.", updated_at = '".date('Y-m-d H:i:s', time())."' WHERE id = ".$sku_id." AND total >= ".$num;
			}
		    else{
			    $goodSql = "UPDATE goods SET total = total - ".$num.", sales_volume = sales_volume + ".$num." WHERE id = ".$goods['id']." AND total >= ".$num;
			}
			$this->db->query($goodSql);
			if($this->db->affected_rows()){
				$flag = true;
				
				//消费积分加现金购买
				//订单中保留商品图片等信息  防止以后更改商品信息 导致订单找不到对应的信息
				$param = array(
				    'share_id' => $share['id'],
				    'share_uid' => $share['user_id'],
				    'is_clear_share' => 0,
					'goods_id' => $goods['id'],
					'goods_name' => $goods['name'],
					'goods_face' => $goods['face'],
					'goods_model' => $goods['model'],
				    'sku_id' => $sku_id,
					'num' => $num,
					'point_price' => $goods['point_price'],
					'cash_price' => $goods['share_price'],
					'total_point_price' => $total_point_price,
					'total_cash_price' => $total_cash_price,
				    'logistic_fee' => $logisticFee,
				    'memo' => $comment,
				    'order_id' => $order_id,
				    'order_address' => $address,
				    'status' => 0
				);
		        
				$this->db->insert('orders', $param);
			}
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
	
	public function createShareByHavePayWallet($sku_id, $share, $num, $goods, $total_cash_price, $logisticFee, $comment, $user_id, $order_id, $address, $transaction_id) {
		//事务
        $this->db->trans_begin();
        
	    //先更新库存销量，防止超卖
		$flag = false;
		$total_point_price = 0;
		$shareSql = "UPDATE share_goods SET sales_num = sales_num + ".$num.", rest_num = rest_num - ".$num.", updated_at = '".date('Y-m-d H:i:s', time())."' WHERE id = ".$share['id']." AND rest_num >= ".$num;
		
		$this->db->query($shareSql);
		if($this->db->affected_rows()){
			if($sku_id){
				//规格商品更新销量
				$volume_sql = "UPDATE goods SET total = total - ".$num.", sales_volume = sales_volume + ".$num." WHERE id = ".$goods['id'];
				$this->db->query($volume_sql);
				
				$goodSql = "UPDATE goods_sku SET total = total - ".$num.", updated_at = '".date('Y-m-d H:i:s', time())."' WHERE id = ".$sku_id." AND total >= ".$num;
			}
			else{
			    $goodSql = "UPDATE goods SET total = total - ".$num.", sales_volume = sales_volume + ".$num." WHERE id = ".$goods['id']." AND total >= ".$num;
			}
			$this->db->query($goodSql);
			if($this->db->affected_rows()){
				$flag = true;
				
				//纯积分兑换
				//订单中保留商品图片等信息  防止以后更改商品信息 导致订单找不到对应的信息
				$param = array(
				    'share_id' => $share['id'],
				    'share_uid' => $share['user_id'],
				    'is_clear_share' => 0,
					'goods_id' => $goods['id'],
					'goods_name' => $goods['name'],
					'goods_face' => $goods['face'],
					'goods_model' => $goods['model'],
				    'sku_id' => $sku_id,
					'num' => $num,
					'point_price' => $goods['point_price'],
					'cash_price' => $goods['share_price'],
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
			    $memberSql = "UPDATE member SET commision = commision - ".$total_cash_price.", address = '".$address."' WHERE id = ".$user_id;
				$this->db->query($memberSql);
				
				$this->db->insert('orders', $param);
			}
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
	 * 涉及现金，钱包支付退款
	 * 分享商品退款
	 */
	public function walletRefundOrderShare($data) {
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
        
		//退回用户所用掉的现金，不涉及积分
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
		
		//退回全部现金给购买者
		$memberSql = "UPDATE member SET commision = commision + ".$money." WHERE id = ".$data['user_id'];
		$this->db->query($memberSql);
		
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
	
    public function selectShareById($id){
	    $where = array(
			"id" => $id
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('share_goods');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function dealSharemanager($share) {
		//事务
        $this->db->trans_begin();
        $flag = false;
        $status = 0;
		$param = array(
		    "status" => 1,
		    'clear_time' => date('Y-m-d H:i:s', time()),
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('status', $status);
		$this->db->where('id', $share['id']);
		$this->db->update('share_goods', $param);
		
		if($this->db->affected_rows()){
		    $flag = true;
			$total_point_price = $share['rest_num'] * $share['single_point'];
	        $shareSql = "UPDATE member SET consume_point = consume_point + ".$total_point_price." WHERE id = ".$share['user_id'];
			$this->db->query($shareSql);
			
			$consume_param = array(
	    	    "type" => 14, //1可用积分转消费积分 2积分转让 3充值报单 4兑换商品 5升级服务中心 6分享 7报单抵扣
	    	    "reason" => 2,  //1转出  2转入
			    "point" => $total_point_price,
		    	"user_id" => $share['user_id'],
			    "order_id" => $share['order_id']
			);
			$this->db->insert('consume_record', $consume_param);
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
	
	public function selectNotClearTimeoutShare() {
		$compareTime = date('Y-m-d H:i:s', time() - 7*24*60*60);
		$where = array(
			"status" => 0,
		);
		$and_where = "deleted_at is null AND created_at < '".$compareTime."' AND rest_num > 0";
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('share_goods');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
}