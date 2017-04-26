<?php
class Sms_model extends CI_Model {
	protected $url ="https://dx.ipyy.net/smsJson.aspx";
	protected $account ="xd001332";
	protected $password ="xd00133233";
	
	public function __construct() {
		parent::__construct();
	}
	
	/*
	 * sendtime定时发送时间 为空表示立即发送
	 * extno 扩展子号
	 */
	public function send($mobiles, $content, $sendtime='')
    {
    	$content = $content.'【久赢五洲】';
    	$body=array(
    		'action'=>'send',
    		'userid'=>'',
    		'account'=>$this->account,
    		'password'=>$this->password,
    		'mobile'=>$mobiles,
    		'extno'=>'',
    		'content'=>$content,
    		'sendtime'=>$sendtime				
    	);

    	$ch=curl_init();
    	curl_setopt($ch, CURLOPT_URL, $this->url);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    	$result = curl_exec($ch);
    	curl_close($ch);
    	return $result;
    }
    
	public function insert($mobile, $message, $type) {
		$param = array(
			'mobile' => $mobile,
		    'content' => $message,
		    'type' => $type,
		);
			
		$this->db->insert('sms', $param);
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
	public function update($id) {
		$param = array(
			'status' => 1,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('id', $id);
		$this->db->update('sms', $param);
		
		//当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
}