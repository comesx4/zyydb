<?php

import('@.Class.Crypt_RC4');

/*
	@der 工具类
*/
Class Tool{
	const HASHCODE = '!@#!@#44345';	
	const DNS_TYPE_22CN		= '22cn';
	const DNS_TYPE_SUNDNS	= 'sundns';
	const DNS_TYPE_XINNET	= '';

	/*
		@der 组合返回消息
		@der string $message 消息
		@der isSuccess 是否成功 
		@der $code 状态码 0表示成功
		@der array $data 附带参数
		@return array
	*/
	public static function output($message ,$isSuccess = true ,$code = 0 ,$data = array()){
		if ($isSuccess) {
			$arr = array(
				'code' 	  => 0,
				'message' => $message
			);
		} else {
			$arr = array(
				'code' 	  => $code,
				'error' => $message
			);
		}

		if (!empty($data)) {
			$arr = array_merge($arr, $data);
		}

		return json_encode($arr);
	}

	//加密 密码
	public static function encryptPassword($value,$is_md5_to_key=true){
		$key=$is_md5_to_key?substr(md5(self::HASHCODE),0,24):self::HASHCODE;//if
		
		return self::rc4Encrypt($value,$key,true);
	}//encryptPassword()
	
	//解密 密码
	public static function decryptPassword($value,$is_md5_to_key=true){
		$key=$is_md5_to_key?substr(md5(self::HASHCODE),0,24):self::HASHCODE;//if
		
		return self::rc4Decrypt($value,$key,true);
	}//decryptPassword()
	
	
	
	/**
	 * rc4加密函数
	 */
	public static function rc4Encrypt($value,$key,$is_base64=false,$is_md5_to_key=false){
		$key=$is_md5_to_key?substr(md5($key),0,24):$key;//if
		
		$rc4=new Crypt_RC4();
		$rc4->setKey($key);
		
		if($is_base64){
			return base64_encode($rc4->encrypt($value));
		}else{
			return $rc4->encrypt($value);
		}//if
	}//rc4Encrypt()
	
	/**
	 * rc4解密函数
	 */
	public static function rc4Decrypt($value,$key,$is_base64=false,$is_md5_to_key=false){
		$key=$is_md5_to_key?substr(md5($key),0,24):$key;//if
		
		$rc4=new Crypt_RC4();
		$rc4->setKey($key);
		
		if($is_base64) $value=base64_decode($value);//if
		$value=$rc4->decrypt($value);
		return trim($value);
	}//rc4Decrypt()

	public static function getTimeStampToDate($timestamp,$timeformat=''){
		$db_datefm='Y-m-d H:i:s';
		$db_timedf='8';
		$date_show=$timeformat ? $timeformat : ($_datefm ? $_datefm : $db_datefm);
		if($_timedf)
		{
			$offset = $_timedf=='111' ? 0 : $_timedf;
		}
		else
		{
			$offset = $db_timedf=='111' ? 0 : $db_timedf;
		}
		return gmdate($date_show,$timestamp+$offset*3600);
	}

	//生成随机字符
	public static function randStr($length=32, $length2 = ''){
		$sChars='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$sChars=str_shuffle($sChars);
		$nLength = strlen($sChars)-1;
		
		if ($length2) {
			$length = mt_rand($length , $length2);
		}
		
		$strString='';
		
		for($i=0;$i<$length;++$i){
			$strString.=$sChars[mt_rand(0,$nLength)];
		}//for
		
		return $strString;
	}//generateRandomString()
}