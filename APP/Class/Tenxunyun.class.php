<?php
/* *
 * 类名：Tenxunyun
 * 功能：生成腾讯云API接口公用参数
 * 详细：调用腾讯云接口
 * 版本：未知
 * 日期：2015-6-29
 * 作者：袁宝城
 */
Class Tenxunyun{
	static private $SecretId = 'AKIDlqcoOImR8Z1UNoOWf31klx0J0ODPtS3L';
	static private $SecretKey = 'Is3mpRoemRkdiXPDbbeaWlTqqoAYdSfj';
	//提交的域名(地址)
	static private $Address = 'cvm.api.qcloud.com';
	//提交的方法名称
	static private $Action = '';
	//请求方式(GET或POST)
	static private $dataType = 'GET';
	//当前时间戳
	static private $Timestamp = '';
	//随机正整数
	static private $Nonce = '';
	//区域：gz: 代表广州机房, sh: 代表上海机房, hk: 代表香港机房;
	static private $Region = 'gz';
	//请求时的参数
	static private $requestData = array();

	/*
		@der 给成员设置属性的方法
		@param string $key 成员名称
		@param string $value 值
	*/
	static public function set($key,$value){
		if ( isset(get_class_vars(get_class())[$key]) ) {
			self::$$key = $value;
		}
	}

	/*
		@der 发送请求		
		@param array $requestData [请求时的参数]
		@param array $Action [提交的方法名]
		@param array $Address [提交的域名]
		@return array 结果集
	*/
	static public function sendRequest($requestData='',$Action='',$Address=''){			
		//生成请求地址参数
		$parameter = self::initialization($requestData,$Action,$Address);
		//获取结果集
		$data = self::request($parameter);
		return $data;
	}

	/*
		@der 发送请求的方法
		@param string $parameter 请求地址参数
		@return array 结果集
	*/
	static private function request($parameter){

		$ch = curl_init();
		//获取成功或失败的数字不返回（如果没有这段代码，返回成功还会带着1，失败0）
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 		
		// https请求 不验证证书和hosts
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 

		if (self::$dataType == 'POST') {
			curl_setopt($ch,CURLOPT_URL,'https://'.self::$Address.'/v2/index.php');
			// post数据
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt ( $ch, CURLOPT_HEADER, 0 );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			// post的变量
			curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
		} else {
			$url = 'https://'.self::$Address.'/v2/index.php?'.$parameter;
			curl_setopt($ch,CURLOPT_URL,$url);
		}		
	
		$outpot = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($outpot,true);
	}	

	/*
		@der 初始化参数,并生成请求地址
		@return string 请求地址参数
	*/
	static private function initialization($requestData,$Action,$Address){
		self::$requestData = !empty($requestData)?$requestData:self::$requestData;			
		self::$Action 	   = !empty($Action)?$Action:self::$Action;
		self::$Address     = !empty($Address)?$Address:self::$Address;
		//正整数
		self::$Nonce 	   = self::randStr(8);
		//当前时间戳
		self::$Timestamp   = $_SERVER['REQUEST_TIME'];

		/*组合请求内容*/
		$arr = array(
			'Action'    => self::$Action,
			'SecretId'  => self::$SecretId,
			'Region'    => self::$Region,
			'Timestamp' => self::$Timestamp,
			'Nonce'     => self::$Nonce 
			);		
		$arr = array_merge($arr,self::$requestData);				
		ksort($arr);
		$request = http_build_query($arr);
		/*请求方式 + 请求主机 +请求路径 + ? + 请求字符串 */
		$request = urldecode(self::$dataType.self::$Address."/v2/index.php?".$request); 
		/*生成签名串 */
		$signStr = base64_encode(hash_hmac('sha1', $request, self::$SecretKey, true));
		
		//对签名进行 Url Encode
		$signaTure 	= UrlEncode($signStr);	
		/*生成请求地址的参数*/
		$arr['Signature'] = $signaTure;		
		//$arr = http_build_query($arr);  会出问题（因为这个问题浪费我一下午时间）
		//组合url参数 
		$parameter = '';
		foreach ($arr as $key=>$v) {
			$parameter .="&{$key}={$v}";
		}
		$parameter = ltrim($parameter,'&');
		
		return $parameter;
	}

	/*
		@der 生成随机正整数
		@return string
	*/
	static private function randStr($length=8){
		$str = '123456789';
		$number = '';
		for ($i=0; $i<$length; $i++) {	
			$number .= $str{mt_rand(0,8)};
		}
		return $number;
	}
}