<?php
class Download_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function selectPageData($searchkey, $offset, $num, $uid) {
	    //取总条数
	    //按条件搜索
	    if(!empty($searchkey) && $searchkey != ''){
	    	$sql = "SELECT d.id FROM download AS d INNER JOIN upload AS u ON d.link_id = u.id WHERE (u.setting_name like '%".$searchkey."%' OR u.labellist like '%".$searchkey."%') AND d.uid = ".$uid;
	    }
	    else{
	    	$sql = "SELECT d.id FROM download AS d INNER JOIN upload AS u ON d.link_id = u.id WHERE d.uid = ".$uid;
	    }

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$res = $query->result_array();
			$allCount = count($res);
		}
		else{
			return null;
		}
		
		//取页数据
	    //按条件搜索会员
	    if(!empty($searchkey) && $searchkey != ''){
			$sql = "SELECT u.*, d.created_at as time FROM download AS d INNER JOIN upload AS u ON d.link_id = u.id WHERE (u.setting_name like '%".$searchkey."%' OR u.labellist like '%".$searchkey."%') ADN d.uid = ".$uid." ORDER BY d.created_at DESC LIMIT ".$offset.",".$num;
		}
		else{
			$sql = "SELECT u.*, d.created_at as time FROM download AS d INNER JOIN upload AS u ON d.link_id = u.id WHERE d.uid = ".$uid." ORDER BY d.created_at DESC LIMIT ".$offset.",".$num;
		}
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$return = $query->result_array();
		    return array("data"=>$return, "count"=>$allCount);
		}
		else{
			return null;
		}
	}
	
	public function download($id, $num, $uid) {
		$paramDownload = array(
			'link_id' => $id,
		    "uid" => $uid,
		    "created_at" => date('Y-m-d H:i:s', time())
		);
		
		$paramUpload = array(
		    "download_num" => $num,
		    "updated_at" => date('Y-m-d H:i:s', time())
		);
			
		$this->db->trans_begin();
		$this->db->insert('download', $paramDownload);
		$this->db->where('id', $id);
		$this->db->update('upload', $paramUpload);
	
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
	
}