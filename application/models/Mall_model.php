<?php
class Mall_model extends CI_Model {
	public function __construct() {
		parent::__construct();
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
	
    public function selectCompanyData() {
        $where = 'deleted_at is null';
        $status_where = array(
			"is_company" => 1
		);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($status_where);

		$query = $this->db->get('member');
	    if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function getAlreadyBuyNum($goods_id, $user_id) {
		//$start = date('Y-m-d H:i:s', time() - 30*24*60*60);
        //$where = "deleted_at is null AND created_at >= '".$start."' AND status IN (0, 1, 2, 3)";
        $where = "deleted_at is null AND status IN (0, 1, 2, 3)";
        $and_where = array(
			"goods_id" => $goods_id,
            "user_id" => $user_id
		);
		
		$this->db->select_sum('num');
		$this->db->where($where);
		$this->db->where($and_where);
			
		$query = $this->db->get('orders');
	    if ($query->num_rows() > 0){
	    	$data = $query->row_array();
			return $data['num'] ? $data['num'] : 0;
		}
		else{
			return 0;
		}
	}
	
	public function getCategoryByPath($path) {
        $where = "deleted_at is null";
        $and_where = array(
			"path" => $path
		);
		
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($and_where);
			
		$query = $this->db->get('tree');
	    if ($query->num_rows() > 0){
	    	$data = $query->row_array();
			return $data;
		}
		else{
			return null;
		}
	}
	
    public function selectHomeCategory() {
        $where = 'deleted_at is null';
        $and_where = array(
			"is_open" => 1
		);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($and_where);
		$this->db->order_by('orderby', 'ASC');

		$query = $this->db->get('tree');
	    if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function selectHomeGoods($path){
	    $where = 'deleted_at is null';
        $and_where = array(
			"is_release" => 1,
            "is_recommend" => 1
		);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where($and_where);
		$this->db->like('category', $path, 'after');
		$this->db->order_by('sort_id', 'DESC');
		$this->db->limit(20, 0);

		$query = $this->db->get('goods');
	    if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function do_share($goods, $user_id, $order_id, $num){
	    //事务
        $this->db->trans_begin();
        
	    //先更新总分享量，防止超卖
		$flag = false;
		$id = '';
		$total_point_price = $num * $goods['point_price'];
		$memberSql = "UPDATE member SET consume_point = consume_point - ".$total_point_price." WHERE id = ".$user_id." AND consume_point >= ".$total_point_price;
		
		$this->db->query($memberSql);
		if($this->db->affected_rows()){
			$goodSql = "UPDATE goods SET share_num = share_num + ".$num." WHERE id = ".$goods['id']." AND total >= ".$num;
			$this->db->query($goodSql);
			if($this->db->affected_rows()){
				$flag = true;
				
				$paramShare = array(
					"goods_id" => $goods['id'], 
				    "share_num" => $num,
				    "rest_num" => $num,
				    "share_price" => $goods['share_price'],
				    "single_point" => $goods['point_price'],
			    	"user_id" => $user_id,
				    "order_id" => $order_id
				);
				$this->db->insert('share_goods', $paramShare);
				$id = $this->db->insert_id();
			
				$paramPoint = array(
					"type" => 13,  //可用积分转消费积分 2积分转让 3下线充值返利 4兑换商品 …… 13商品分享
				    "point" => $total_point_price,
				    "reason" => 1, //转出
			    	"user_id" => $user_id,
				    "order_id" => $order_id
				);
				$this->db->insert('consume_record', $paramPoint);
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
		    return $id;
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
	
	public function share_data_by_page($user_id, $searchkey, $page, $num) {	
		$offset = ($page - 1) * $num;
	    $where = 'share_goods.deleted_at is null';
	    $and_where = array(
			"share_goods.user_id" => $user_id
		);

		//总数
		$this->db->select('count(share_goods.id) AS total');
		$this->db->from('share_goods');
		$this->db->join('goods', 'share_goods.goods_id = goods.id');
		$this->db->where($where);
		$this->db->where($and_where);
		
		if($searchkey){
			$this->db->group_start();
		    $this->db->like('goods.goods_name', $searchkey);
		    $this->db->or_like('goods.order_id', $searchkey);
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
		$this->db->select('share_goods.*, goods.name, goods.share_price, goods.img_data');
		$this->db->from('share_goods');
		$this->db->join('goods', 'share_goods.goods_id = goods.id');
		$this->db->where($where);
		$this->db->where($and_where);
	
		if($searchkey){
			$this->db->group_start();
		    $this->db->like('goods.goods_name', $searchkey);
		    $this->db->or_like('goods.order_id', $searchkey);
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
	
	public function selectShareOrderById($id) {
		$where = array(
			"orders.share_id" => $id
		);
		$and_where = 'orders.deleted_at is null AND orders.share_id != 0 AND orders.share_uid != 0';
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
	