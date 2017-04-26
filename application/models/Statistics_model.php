<?php
class Statistics_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function graphData($dep, $year) {
		if($year != 99){
			$begin = $year.'-01-01 00:00:00';
			$end = $year.'-12-31 23:59:59';
			if($dep == 99){
			    $sql = "SELECT * FROM upload WHERE deleted_at is null AND created_at >= '".$begin."' AND created_at <= '".$end."'";
			}
			else{
				$sql = "SELECT * FROM upload WHERE deleted_at is null AND dep_id = ".$dep." AND created_at >= '".$begin."' AND created_at <= '".$end."'";
			}
		}
		else{
		    if($dep == 99){
			    $sql = "SELECT * FROM upload WHERE deleted_at is null";
			}
			else{
				$sql = "SELECT * FROM upload WHERE deleted_at is null AND dep_id = ".$dep;
			}
		}
		
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function uploadNumRank($dep, $year) {
		if($year != 99){
			$begin = $year.'-01-01 00:00:00';
			$end = $year.'-12-31 23:59:59';
			if($dep == 99){
			    $sql = "SELECT * FROM (SELECT author_id, count(author_id) AS num FROM upload WHERE deleted_at is null AND created_at >= '".$begin."' AND created_at <= '".$end."' GROUP BY author_id) AS temp ORDER BY num DESC LIMIT 0,10";
			}
			else{
				$sql = "SELECT * FROM (SELECT author_id, count(author_id) AS num FROM upload WHERE deleted_at is null AND dep_id = ".$dep." AND created_at >= '".$begin."' AND created_at <= '".$end."' GROUP BY author_id) AS temp ORDER BY num DESC LIMIT 0,10";
			}
		}
		else{
		    if($dep == 99){
			    $sql = "SELECT * FROM (SELECT author_id, count(author_id) AS num FROM upload WHERE deleted_at is null GROUP BY author_id) AS temp ORDER BY num DESC LIMIT 0,10";
			}
			else{
				$sql = "SELECT * FROM (SELECT author_id, count(author_id) AS num FROM upload WHERE deleted_at is null AND dep_id = ".$dep." GROUP BY author_id) AS temp ORDER BY num DESC LIMIT 0,10";
			}
		}
		
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function downloadNumRank($dep, $year) {
		if($year != 99){
			$begin = $year.'-01-01 00:00:00';
			$end = $year.'-12-31 23:59:59';
			if($dep == 99){
			    $sql = "SELECT * FROM upload WHERE deleted_at is null AND created_at >= '".$begin."' AND created_at <= '".$end."' AND status = 1 AND download_num > 0 ORDER BY download_num DESC, id DESC LIMIT 0,10";
			}
			else{
				$sql = "SELECT * FROM upload WHERE deleted_at is null AND dep_id = ".$dep." AND created_at >= '".$begin."' AND created_at <= '".$end."' AND status = 1 AND download_num > 0 ORDER BY download_num DESC, id DESC LIMIT 0,10";
			}
		}
		else{
		    if($dep == 99){
			    $sql = "SELECT * FROM upload WHERE deleted_at is null AND status = 1 AND download_num > 0 ORDER BY download_num DESC, id DESC LIMIT 0,10";
			}
			else{
				$sql = "SELECT * FROM upload WHERE deleted_at is null AND dep_id = ".$dep." AND status = 1 AND download_num > 0 ORDER BY download_num DESC, id DESC LIMIT 0,10";
			}
		}
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
	
	public function labelNumRank($dep, $year) {
		if($year != 99){
			$begin = $year.'-01-01 00:00:00';
			$end = $year.'-12-31 23:59:59';
			if($dep == 99){
			    $sql = "SELECT * FROM upload WHERE deleted_at is null AND created_at >= '".$begin."' AND created_at <= '".$end."' AND status = 1 LIMIT 0,10";
			}
			else{
				$sql = "SELECT * FROM upload WHERE deleted_at is null AND dep_id = ".$dep." AND created_at >= '".$begin."' AND created_at <= '".$end."' AND status = 1 LIMIT 0,10";
			}
		}
		else{
		    if($dep == 99){
			    $sql = "SELECT * FROM upload WHERE deleted_at is null AND status = 1 LIMIT 0,10";
			}
			else{
				$sql = "SELECT * FROM upload WHERE deleted_at is null AND dep_id = ".$dep." AND status = 1 LIMIT 0,10";
			}
		}
	    $query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return null;
		}
	}
}