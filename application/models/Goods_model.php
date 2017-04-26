<?php
class Goods_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
   /**
	 * 插入单条记录...
	 */
	public function insertRow($data) {
	    //保存到商品表里的options删除库存字段
	    $total = 0;
		$saveOptionArray = json_decode($data['options'], true);
		foreach($saveOptionArray['optionList'] as $key=>$item){
			$total = $total + $saveOptionArray['optionList'][$key]['num'];
			unset($saveOptionArray['optionList'][$key]['num']);
		}
		
		$param = array(
			'name' => $data['name'],
			"options"=> json_encode($saveOptionArray),
		    'description'=> $data['description'],
		    'market_price'=> $data['market_price'],
		    "point_price"=> $data['point_price'],
		    "cash_price"=> $data['cash_price'],
		    'total' => $data['total'],
		    "model"=> $data['model'],
			"category"=> $data['category'],
			"is_release"=> $data['is_release'],
		    "img_data"=> $data['img_data'],
		    "weight"=> $data['weight'],
		    "limit_buy"=> $data['limit_buy'],
		    "send_method"=> $data['send_method'],
		    "sort_id"=> $data['sort_id'],
			"company_id"=> $data['company_id'],
		    "company_get_type"=> $data['company_get_type'],
			"company_useable_point"=> $data['company_useable_point'],
		    "is_time_buy"=> $data['is_time_buy'],
		    "buy_time"=> $data['buy_time'],
		    "is_share"=> $data['is_share'],
		    "share_price"=> $data['share_price'],
		    "send_type"=> $data['send_type'] ? $data['send_type'] : '1,2',
		    "updated_at"=>date('Y-m-d H:i:s')
		);
			
		$optionArray = json_decode($data['options'], true);
		if($optionArray['is_option'] == 1){
			$this->db->trans_begin();
			$param['total'] = $total;
			$this->db->insert('goods', $param);
			$goods_id = $this->db->insert_id();
			foreach($optionArray['optionList'] as $item){
				$param_sku = array(
				    'goods_id'=>$goods_id,
					'attributes'=>$item['attr_id'],
					'total'=>$item['num']
				);
				$this->db->insert('goods_sku', $param_sku);
			}
			
			//事务
		    if ($this->db->trans_status() === FALSE)
			{
				// 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
				$this->db->trans_rollback();
				return false;
			}
			else
			{
				$this->db->trans_commit();
				return $goods_id;
			}
		}
		else{
			$this->db->insert('goods', $param);
			if($this->db->affected_rows()){
			    return $this->db->insert_id();
			}
			else{
				return false;
			}
		}
	}
	
	public function updateRow($data, $id) {
		//保存到商品表里的options删除库存字段
		$total = 0;
		$saveOptionArray = json_decode($data['options'], true);
		foreach($saveOptionArray['optionList'] as $key=>$item){
			$total = $total + $saveOptionArray['optionList'][$key]['num'];
			unset($saveOptionArray['optionList'][$key]['num']);
		}

		$param = array(
			'name' => $data['name'],
		    "options"=> json_encode($saveOptionArray),
		    'description'=> $data['description'],
		    'market_price'=> $data['market_price'],
		    "point_price"=> $data['point_price'],
		    "cash_price"=> $data['cash_price'],
		    'total' => $data['total'],
		    "model"=> $data['model'],
			"category"=> $data['category'],
			"is_release"=> $data['is_release'],
		    "img_data"=> $data['img_data'],
		    "send_method"=> $data['send_method'],
		    "weight"=> $data['weight'],
		    "limit_buy"=> $data['limit_buy'],
		    "company_id"=> $data['company_id'],
		    "company_get_type"=> $data['company_get_type'],
			"company_useable_point"=> $data['company_useable_point'],
		    "send_type"=> $data['send_type'] ? $data['send_type'] : '1,2',
		    "buy_time"=> $data['buy_time'],
		    "is_time_buy"=> $data['is_time_buy'],
		    "is_share"=> $data['is_share'],
		    "share_price"=> $data['share_price'],
		    "updated_at"=>date('Y-m-d H:i:s')
		);
		
		$optionArray = json_decode($data['options'], true);
		if($optionArray['is_option'] == 1){
			$this->db->trans_begin();
			$param['total'] = $total;
			$this->db->where('id', $id);
			$this->db->update('goods', $param);
			
			//删除原有的记录
			$param_del_sku = array(
			    "deleted_at"=>date('Y-m-d H:i:s')
			);
			$this->db->where('goods_id', $id);
			$this->db->update('goods_sku', $param_del_sku);
			
		    foreach($optionArray['optionList'] as $item){
				$param_sku = array(
				    'goods_id'=>$id,
					'attributes'=>$item['attr_id'],
					'total'=>$item['num']
				);
				$this->db->insert('goods_sku', $param_sku);
			}
			
		    //事务
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
			$this->db->where('id', $id);
			$this->db->update('goods', $param);
			
			//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
			if($this->db->affected_rows()){
				return true;
			}
			else{
				return false;
			}
		}
	}
	
	public function selectPageData($status, $path, $start, $end, $searchkey, $offset, $num, $is_company){	
	    $where = 'goods.deleted_at is null';

		//总数
		$this->db->select('count(goods.id) AS total');
		$this->db->from('goods');
		$this->db->join('member', 'member.id = goods.company_id', 'left');
		$this->db->where($where);
		
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " goods.created_at >= '".$begin."' AND goods.created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
		
	    if($status != 99){
	    	if($status == 1){
				$status_where = array(
					"goods.is_release" => 1
				);
				$this->db->where($status_where);
	    	}
	        elseif($status == 2){
				$status_where = array(
					"goods.is_release" => 0
				);
				$this->db->where($status_where);
	    	}
	    	elseif($status == 3){
				$status_where = array(
					"goods.is_recommend" => 1
				);
				$this->db->where($status_where);
	    	}
	        elseif($status == 4){
				$status_where = array(
					"goods.is_time_buy" => 1
				);
				$this->db->where($status_where);
	    	}
	        elseif($status == 5){
				$status_where = array(
					"goods.is_share" => 1
				);
				$this->db->where($status_where);
	    	}
		}
		
	    if($is_company != 99){
	    	if($is_company == 1){
			    $this->db->where('goods.company_id > ', 0);
	    	}
	    	else{
	    		$this->db->where('goods.company_id', 0);
	    	}
		}

	    if($searchkey){
	    	$this->db->group_start();
		    $this->db->like('goods.name', $searchkey);
		    $this->db->or_like('member.name', $searchkey);
		    $this->db->or_like('member.mobile', $searchkey);
		    $this->db->group_end();
		}
		$this->db->like('goods.category', $path, 'after');
		
		$queryAll = $this->db->get();
		if ($queryAll->num_rows() > 0){
			$data = $queryAll->row_array();
			$count = $data['total'];
		}
		else{
			$count = 0;
		}
		
		//分页
		$this->db->select('goods.*');
		$this->db->from('goods');
		$this->db->join('member', 'member.id = goods.company_id', 'left');
		$this->db->where($where);
	    if($start && $end){
	    	$begin = $start.' 00:00:00';
	    	$over = $end.' 23:59:59';
	    	$time_where = " goods.created_at >= '".$begin."' AND goods.created_at <= '".$over."'";
			$this->db->where($time_where);
	    }
		
	    if($status != 99){
	    	if($status == 1){
				$status_where = array(
					"goods.is_release" => 1
				);
				$this->db->where($status_where);
	    	}
	        elseif($status == 2){
				$status_where = array(
					"goods.is_release" => 0
				);
				$this->db->where($status_where);
	    	}
	    	elseif($status == 3){
				$status_where = array(
					"goods.is_recommend" => 1
				);
				$this->db->where($status_where);
	    	}
	        elseif($status == 4){
				$status_where = array(
					"goods.is_time_buy" => 1
				);
				$this->db->where($status_where);
	    	}
	        elseif($status == 5){
				$status_where = array(
					"goods.is_share" => 1
				);
				$this->db->where($status_where);
	    	}
		}
		
	    if($is_company != 99){
	    	if($is_company == 1){
			    $this->db->where('goods.company_id > ', 0);
	    	}
	    	else{
	    		$this->db->where('goods.company_id', 0);
	    	}
		}

	    if($searchkey){
			$this->db->group_start();
		    $this->db->like('goods.name', $searchkey);
		    $this->db->or_like('member.name', $searchkey);
		    $this->db->or_like('member.mobile', $searchkey);
		    $this->db->group_end();
		}
		$this->db->like('goods.category', $path, 'after');
		$this->db->order_by('goods.sort_id', 'DESC');
		$this->db->order_by('goods.id', 'DESC');
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
	
	public function deleteRow($id) {
	    $data = array(
			'deleted_at' => date('Y-m-d H:i:s', time()),
		);
		
		$this->db->where('id', $id);
		$this->db->update('goods', $data);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function deleteGoodsImg($id, $file) {
		$goodsData = $this->selectDataById($id);
		if($goodsData !== null){
		    $imgData = json_decode($goodsData['img_data']);
		    foreach($imgData as $key=>$item){
		    	if($item->file == $file){
		    		unset($imgData[$key]);
		    	}
		    }
		    
		    //sort 解决多维数组json_encode之后产生对象而不是数组问题 
		    sort($imgData);
		    $param = array(
			    "img_data"=> json_encode($imgData)
			);
			
			$this->db->where('id', $id);
			$this->db->update('goods', $param);
			
			//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
			if($this->db->affected_rows()){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
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
		    ->get('goods');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function list_page_info($path, $sort, $searchkey, $offset, $num) {	
	    $where = 'deleted_at is null AND is_release = 1';

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		
		if($searchkey){
		    $this->db->like('name', $searchkey);
		}
		if($path != 'all'){
			$this->db->like('category', $path, 'after');
			if($path == '1/76'){
				//新品推荐只显示7天内上架的商品
				$begin = date('Y-m-d H:i:s', time() - 7 *24 * 60 * 60);
		    	$end = date('Y-m-d H:i:s', time());;
		    	$time_where = " updated_at >= '".$begin."' AND updated_at <= '".$end."'";
				$this->db->where($time_where);
			}
		}
		
		$queryAll = $this->db->get('goods');
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
	
		if($searchkey){
		    $this->db->like('name', $searchkey);
		}
	    if($path != 'all'){
			$this->db->like('category', $path, 'after');
	        if($path == '1/76'){
				//新品推荐只显示7天内上架的商品
				$begin = date('Y-m-d H:i:s', time() - 7 *24 * 60 * 60);
		    	$end = date('Y-m-d H:i:s', time());;
		    	$time_where = " updated_at >= '".$begin."' AND updated_at <= '".$end."'";
				$this->db->where($time_where);
			}
		}
		
		if($sort == 1){
			$this->db->order_by('sort_id', 'DESC');
		}
		elseif($sort == 2){
			$this->db->order_by('sales_volume', 'DESC');
		}
	    elseif($sort == 3){
			$this->db->order_by('point_price', 'DESC');
		}
		
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('goods');
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
	
	public function switch_status($id, $value) {
	    $data = array(
	        "is_release" => $value,
			'updated_at' => date('Y-m-d H:i:s', time()),
		);
		
		$this->db->where('id', $id);
		$this->db->update('goods', $data);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function recommend_status($id, $value) {
	    $data = array(
	        "is_recommend" => $value,
			'updated_at' => date('Y-m-d H:i:s', time()),
		);
		
		$this->db->where('id', $id);
		$this->db->update('goods', $data);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function recommend_goods($searchkey, $offset, $num) {	
	    $where = 'deleted_at is null AND is_release = 1 AND is_recommend = 1';

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);
		
	    if($searchkey){
		    $this->db->like('name', $searchkey);
		}
		
		$queryAll = $this->db->get('goods');
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
	
		if($searchkey){
		    $this->db->like('name', $searchkey);
		}
		$this->db->order_by('sort_id', 'DESC');
		$this->db->order_by('id', 'DESC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('goods');
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
	
	public function category() {
		$where = array(
			"is_root" => 0
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->order_by('orderby', 'ASC')
		    ->get('tree');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
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
		$this->db->update('goods', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function selectLogisticDataById($id) {
		$where = array(
			"id" => $id
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('logistic');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function selectDataByOffset($offset){
	    $sql = "SELECT * FROM goods WHERE deleted_at is null ORDER BY sort_id DESC LIMIT ".$offset.",1";
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
	public function changeSortEachother($first_id, $first_sort_id, $second_id, $second_sort_id) {
		$paramFirst = array(
		    "sort_id"=>$second_sort_id,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$paramSecond = array(
		    "sort_id"=>$first_sort_id,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->trans_begin();
		$this->db->where('id', $first_id);
		$this->db->update('goods', $paramFirst);
		$this->db->where('id', $second_id);
		$this->db->update('goods', $paramSecond);
		
	    //事务
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
	
	public function selectMaxSortRow() {
		$sql = "SELECT * FROM goods WHERE deleted_at is null ORDER BY sort_id DESC LIMIT 0,1";
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->row_array();
		    return $return;
		}
		else{
			return null;
		}
	}
	
	public function changeSortToNew($id, $new_sort_id) {
		$param = array(
		    'sort_id' => $new_sort_id,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $id);
		$this->db->update('goods', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function selectSkuDataByGidNotZero($gid) {
		$where = array(
			"goods_id" => $gid
		);
		$and_where = 'deleted_at is null AND total > 0';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('goods_sku');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function selectSkuDataByGidWithinZero($gid) {
		$where = array(
			"goods_id" => $gid
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('goods_sku');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function selectSkuRow($gid, $attr_id) {
		$where = array(
			"goods_id" => $gid,
		    "attributes" => $attr_id
		);
		$and_where = 'deleted_at is null AND total > 0';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->where($and_where)
		    ->get('goods_sku');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
	public function selectSkuRowById($sku_id) {
		$where = array(
		    "id" => $sku_id
		);
		$and_where = 'deleted_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    //->where($and_where)
		    ->get('goods_sku');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->row_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
    public function getUpdateTimeNullData() {
		$where = 'updated_at is null';
		
		$query = $this->db->select('*')
		    ->where($where)
		    ->get('goods');
		
	    if ($query->num_rows() > 0){
	    	$result = $query->result_array();
	    	return $result;
		}
		else{
			return null;
		}
	}
	
    public function modifyUpdateTime($data) {
		$param = array(
			"updated_at"=>$data['created_at']
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('goods', $param);
		
		if($this->db->affected_rows()){
	    	return true;
		}
		else{
			return false;
		}
	}
	
	public function timebuy_page_data($offset, $num) {	
	    $where = 'deleted_at is null AND is_release = 1 AND is_time_buy = 1';

		//总数
		$this->db->select('count(id) AS total');
		$this->db->where($where);

		$queryAll = $this->db->get('goods');
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
		$this->db->order_by('buy_time', 'ASC');
		$this->db->limit($num, $offset);
		
		$query = $this->db->get('goods');
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
}