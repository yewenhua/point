<?php
class Log_model extends CI_Model {
	const PATH  = 'log.txt';
	public function __construct() {
		parent::__construct();
	}
	
	public function insert_operate_log($operater_id, $operater_name, $operater_desc){
		$operater_ip = $this->get_client_ip();
	    $param = array(
			"operater_id"=>$operater_id,
		    "operater_name" => $operater_name,
		    "operater_desc" => $operater_desc,
	        "operater_ip" => $operater_ip,
		);
			
		$this->db->insert('operate_log', $param);
		if($this->db->affected_rows()){
		    return $this->db->insert_id();
		}
		else{
			return null;
		}
	}
	
   /**
	 * 获取客户端IP...
	 */
	function get_client_ip()
	{
		if ($_SERVER['REMOTE_ADDR']) {
		    $cip = $_SERVER['REMOTE_ADDR'];
		} 
		elseif (getenv("REMOTE_ADDR")) {
		    $cip = getenv("REMOTE_ADDR");
		} 
		elseif (getenv("HTTP_CLIENT_IP")) {
		    $cip = getenv("HTTP_CLIENT_IP");
		} 
		else {
		    $cip = "133.126.181.12";
		}
		return $cip;
	}
	
   /**
	 * 管理员获取日志管理记录...
	 */
	public function logPageData($time, $searchkey, $offset, $num) {
	    //取总条数
	    //是否按时间搜索
	    if($time){
	    	$start = $time." 00:00:00";
	    	$end = $time." 23:59:59";
		    if(!empty($searchkey) && $searchkey != ''){
			    $sql = "SELECT * FROM operate_log WHERE (operater_name like '%".$searchkey."%' OR operater_desc like '%".$searchkey."%') AND created_at>='".$start."' AND created_at<='".$end."' AND deleted_at is null";
		    }
		    else{
		    	$sql = "SELECT * FROM operate_log WHERE created_at >= '".$start."' AND created_at <= '".$end."' AND deleted_at is null";
		    }
	    }
	    else{
	        if(!empty($searchkey) && $searchkey != ''){
			    $sql = "SELECT * FROM operate_log WHERE (operater_name like '%".$searchkey."%' OR operater_desc like '%".$searchkey."%') AND deleted_at is null";
		    }
		    else{
		    	$sql = "SELECT * FROM operate_log WHERE deleted_at is null";
		    }
	    }
		$query = $this->db->query($sql);
		if ($query->num_rows() >= 0){
			$all = $query->result_array();
			$allCount = count($all);
		}
		else{
			return null;
		}
		
		//是否按时间搜索
		if($time){
			$start = $time." 00:00:00";
	    	$end = $time." 23:59:59";
			if(!empty($searchkey) && $searchkey != ''){
			    $sql = "SELECT * FROM operate_log WHERE (operater_name like '%".$searchkey."%' OR operater_desc like '%".$searchkey."%') AND created_at>='".$start."' AND created_at<='".$end."' AND deleted_at is null ORDER BY id DESC LIMIT ".$offset.",".$num;
			}
			else{
				$sql = "SELECT * FROM operate_log WHERE created_at>='".$start."' AND created_at<='".$end."' AND  deleted_at is null ORDER BY id DESC LIMIT ".$offset.",".$num;
			}
		}
		else{
		    if(!empty($searchkey) && $searchkey != ''){
			    $sql = "SELECT * FROM operate_log WHERE (operater_name like '%".$searchkey."%' OR operater_desc like '%".$searchkey."%') AND deleted_at is null ORDER BY id DESC LIMIT ".$offset.",".$num;
			}
			else{
				$sql = "SELECT * FROM operate_log WHERE deleted_at is null ORDER BY id DESC LIMIT ".$offset.",".$num;
			}
		}
		$query = $this->db->query($sql);
		if ($query->num_rows() >= 0){
			$return = $query->result_array();
		    return array("data"=>$return, "count"=>$allCount);
		}
		else{
			return null;
		}
	}
	
    public function mkdir($path, $chmod=0777)
	{
		return is_dir($path) or ($this->mkdir(dirname($path),$chmod) and mkdir($path,$chmod));
	}
	
    public function write($logs){
        if(!empty($logs)){
	        if(!is_array($logs))
	        {
	            $content = date('H:i:s',time())."\t".$logs."\t\r\n----------------------------------\r\n";
	        }
	        else{
	            $content = date('H:i:s',time())."\t".join("\t",$logs."\t\r\n")."----------------------------------\r\n";
	        }
	
	        //生成路径
	        $fileName = APPPATH.'../application/logs/'.date('Y-m-d H',time()).'_'.self::PATH;
	        if(!file_exists($dirname = dirname($fileName)))
	        {
	            $this->mkdir($dirname);
	        }
	
	        $result = error_log($content, 3 ,$fileName);
	        $this->remove_old_file();
	
	        if($result)
	        {
	            return true;
	        }
	        else
	        {
	            return false;
	        }
        }
    }
    
    public function get(){
    	$fileName = BASEPATH.'../application/logs/'.date('Y-m-d H',time()).'_'.self::PATH;
        if(file_exists($fileName)){
             return str_replace("\r\n","<br/>", file_get_contents($fileName)); //读取文件中的内容;
        }
        else{
        	return '';
        }
    }
    
    public function clean(){
    	$fileName = BASEPATH.'../application/logs/'.date('Y-m-d H',time()).'_'.self::PATH;
        if(file_exists($fileName)){
            file_put_contents($fileName, '');
        }
    }
    
    public function remove_old_file(){
    	$log_path = BASEPATH.'../application/logs/';
		$current_dir = @opendir($log_path);
		while ($filename = @readdir($current_dir))
		{
			if($filename != '.' && $filename != '..'){
				$file_base_array = explode('.', $filename);
				$file_base_time_array = explode('_', $file_base_array[0]);
				$file_time = strtotime($file_base_time_array[0].':00:00') + 24*60*60*3;
				if($file_time < time()){
				    @unlink($log_path.$filename);
				}
			}
		}

		@closedir($current_dir);
    }
	
}