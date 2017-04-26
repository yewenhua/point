<?php
class Program_model extends CI_Model {
	private $appid = 'wx4f4bc4dec97d474b';
	private $sessionKey = 'tiihtNczf5v6AKRyjwEUhQ==';
	
	private $key = '';
	public static $block_size = 16;
	
	public static $OK = 0;
	public static $IllegalAesKey = -41001;
	public static $IllegalIv = -41002;
	public static $IllegalBuffer = -41003;
	public static $DecodeBase64Error = -41004;
	
	public function todo(){
		$appid = $this->appid;
		$sessionKey = $this->sessionKey;
		
		$encryptedData="CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZM
		                QmRzooG2xrDcvSnxIMXFufNstNGTyaGS
		                9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+
		                3hVbJSRgv+4lGOETKUQz6OYStslQ142d
		                NCuabNPGBzlooOmB231qMM85d2/fV6Ch
		                evvXvQP8Hkue1poOFtnEtpyxVLW1zAo6
		                /1Xx1COxFvrc2d7UL/lmHInNlxuacJXw
		                u0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
		                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs
		                8LOddcQhULW4ucetDf96JcR3g0gfRK4P
		                C7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB
		                6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
		                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFd
		                lqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYV
		                oKlaRv85IfVunYzO0IKXsyl7JCUjCpoG
		                20f0a04COwfneQAGGwd5oa+T8yO5hzuy
		                Db/XcxxmK01EpqOyuxINew==";
		
		$iv = 'r7BXXKkLb8qrSNn05n0qiA==';
		
		$errCode = $this->validateData($encryptedData, $iv, $data );
		
		if ($errCode == 0) {
		    print($data . "\n");
		} else {
		    print($errCode . "\n");
		}
	}
	
    /**
	 * 检验数据的真实性，并且获取解密后的明文.
	 * @param $encryptedData string 加密的用户数据
	 * @param $iv string 与用户数据一同返回的初始向量
	 * @param $data string 解密后的原文
	 * @return int 成功0，失败返回对应的错误码
	 */
	private function validateData( $encryptedData, $iv, &$data )
	{
		if (strlen($this->sessionKey) != 24) {
			return self::$IllegalAesKey;
		}
		$aesKey = base64_decode($this->sessionKey);
        
		if (strlen($iv) != 24) {
			return self::$IllegalIv;
		}
		
		$aesIV = base64_decode($iv);
		$aesCipher = base64_decode($encryptedData);

		$result = $this->decrypt($aesCipher, $aesIV, $aesKey);
        
		if ($result[0] != 0) {
			return $result[0];
		}
     
        $dataObj = json_decode( $result[1] );
        if( $dataObj  == NULL )
        {
            return self::$IllegalBuffer;
        }
        if( $dataObj->watermark->appid != $this->appid )
        {
            return self::$IllegalBuffer;
        }
		$data = $result[1];
		return self::$OK;
	}
	
   /**
	 * 对密文进行解密
	 * @param string $aesCipher 需要解密的密文
     * @param string $aesIV 解密的初始向量
	 * @return string 解密得到的明文
	 */
	public function decrypt( $aesCipher, $aesIV, $key )
	{
		$this->key = $key;
		try {
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			mcrypt_generic_init($module, $this->key, $aesIV);

			//解密
			$decrypted = mdecrypt_generic($module, $aesCipher);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);
		} catch (Exception $e) {
			return array(self::$IllegalBuffer, null);
		}

		try {
			//去除补位字符
			$result = $this->PKCS7Encoder($decrypted);
		} catch (Exception $e) {
			return array(self::$IllegalBuffer, null);
		}
		return array(0, $result);
	}
	
   /**
	 * 对需要加密的明文进行填充补位
	 * @param $text 需要进行填充补位操作的明文
	 * @return 补齐明文字符串
	 */
	function PKCS7Encoder( $text )
	{
		$block_size = self::$block_size;
		$text_length = strlen( $text );
		//计算需要填充的位数
		$amount_to_pad = self::$block_size - ( $text_length % self::$block_size );
		if ( $amount_to_pad == 0 ) {
			$amount_to_pad = self::block_size;
		}
		//获得补位所用的字符
		$pad_chr = chr( $amount_to_pad );
		$tmp = "";
		for ( $index = 0; $index < $amount_to_pad; $index++ ) {
			$tmp .= $pad_chr;
		}
		return $text . $tmp;
	}

	/**
	 * 对解密后的明文进行补位删除
	 * @param decrypted 解密后的明文
	 * @return 删除填充补位后的明文
	 */
	function PKCS7Decode($text)
	{

		$pad = ord(substr($text, -1));
		if ($pad < 1 || $pad > 32) {
			$pad = 0;
		}
		return substr($text, 0, (strlen($text) - $pad));
	}
}