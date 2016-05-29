<?php
/* *
 * 类名：Telephone
 * 功能：发送手机短信
 * 详细：调用中国联通HTTP接口发送短信
 * 版本：3.5.0
 * 日期：2015-6-25
 * 作者：袁宝城
 */
Class Telephone{
	//提交地址
	static private $url = 'http://ums.zj165.com:8888/sms/Api/Send.do';
	//提交类型，GET或POST（暂时无效）
	static private $dataType = 'POST';
	//企业编号
	static private $SpCode = '003096';
	//用户名称
	static private $LoginName = 'zj_kztx';
	//用户密码
	static private $Password = 'T8q6T5!@#66';
	//短信内容(暂时无效)
	static private $MessageContent = '你有一项编号为12345的事务需要处理。';
	//手机号码(多个号码用”,”分隔)
	static private $UserNumber = '';
	//流水号（20位数字）
	static private $SerialNumber = '';
	//预约发送时间，立即发送为空
	static private $ScheduleTime = '';
	//接入扩展号，默认不填写（暂时无效）
	static private $ExtendAccessNum = '';
	//提交检测方式，为1时：提交号码中有效的号码仍正常发出短信，无效的号码在返回参数faillist中列出,反之只要有错误号码就全部不提交
	static private $f = '1';
	//错误消息
	static private $error = '';
	//短信记录表的数据库连接
	static private $db;
	//同一IP一小时内的短信发送次数
	static private $ipSum = 10;
	//同一手机一天的短信发送次数
	static private $telephoneSum = 15;
	//验证码有效时间
	static private $verifyTime = 180;

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
		@der 发送短信的方法
		@param string $telephone [手机号码]
	*/
	static public function sendInformation($telephone=''){

		self::$UserNumber = !empty($telephone)?$telephone:self::$UserNumber;
		if (!self::check()) {
			return self::$error;
		}

		$url = 'http://www.chinaccnet.com/api.php?uid=ms1289529157&sign=beb00727666389d71fe78a83b5d8db6b&mob='.self::$UserNumber.'&content='.urlencode(self::$MessageContent).'&intime=';

		curl($url);
		self::write();		
		// //这里必须使用&cod=2&（搞了半天就是这个问题）
		// $post_data = http_build_query(array(
		// 	"SpCode" => self::$SpCode,
		// 	"LoginName" => self::$LoginName,			
		// 	"Password" => self::$Password,
		// 	"MessageContent" => self::$MessageContent,
		// 	'UserNumber'=>self::$UserNumber,
		// 	"SerialNumber" => self::$SerialNumber,
		// 	"ScheduleTime" => self::$ScheduleTime,
		// 	"f" => self::$f
		// ));
		
		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, self::$url);
		
		// // post数据
		// curl_setopt($ch, CURLOPT_POST, 1);
		// curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// // post的变量
		// curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		// $output = curl_exec($ch);
		// curl_close($ch);
	
		// //生成短信记录
		// self::write();
	}	

	/*
		@der 删除过期的短信记录
	*/
	static public function delDetail(){
		//删除除了今天的所有记录
		$where = array('time'=>array( 'lt',strtotime(date('Y-m-d')) ));
		M('Information_detail')->where($where)->delete();
	}

	/*
		@der 初始化成员的值，并且进行一系列验证
		@return boolean
	*/
	static private function check(){
		//初始化数据库连接
		self::$db = M('Information_detail');
		//短信轰炸机验证
		if ( !self::checkInformation() ){
			return false;
		}
		//生成随机数字
		self::$SerialNumber = date('YmdHis').self::randStr();		
		//短信信息
		$str = self::randStr(4);
		setcookie("kz_".self::$UserNumber."",encode($str),time()+self::$verifyTime+10,'/');

		self::$MessageContent = "欢迎使用智御云，您本次的验证码为：{$str}（有效时间120秒）";		
		//修改编码
		self::$MessageContent = iconv('utf-8', 'gb2312', self::$MessageContent);

		return true;
	}

	/*
		@der 生成随机数字
		@return string
	*/
	static private function randStr($length=6){
		$str = '0123456789';
		$number = '';
		for ($i=0; $i<$length; $i++) {	
			$number .= $str{rand(0,9)};
		}
		return $number;
	}

	/*
		@der 将发送人的信息写入数据库中
	*/
	static private function write(){

		$arr = array(
			'time'=>$_SERVER['REQUEST_TIME'],
			'ip'=>get_client_ip(),
			'telephone'=>self::$UserNumber
			);
		self::$db->add($arr);
	}

	/*
		@der 对提交者进行验证
			1. 禁止验证码有效时间内重复发送
			2. 限制单个IP地址一段时间内请求验证码的次数；
			3. 限制一天内，对同一手机号码发送验证码的次数；
		@return boolean
	*/
	static private function checkInformation(){

		/*限制验证码有效时间内同一个手机重复获取*/
		$where = array('telephone'=>self::$UserNumber,'time'=>array( 'gt',$_SERVER['REQUEST_TIME']-self::$verifyTime ));
		if (self::$db->where($where)->count() > 0) {
			self::$error = '请勿重复提交';
			return false;
		}
		
		/*限制同一个IP在1小时内的提交次数*/		
		$where = array('ip'=>get_client_ip(),'time'=>array( 'gt',$_SERVER['REQUEST_TIME']-3600));
		if (self::$db->where($where)->count() >= self::$ipSum) {
			self::$error = '您在同一时间内的提交次数过多，请在一小时后重试';
			return false;
		}

		/*限制当天同一手机的提交次数*/
		$where = array('telephone'=>self::$UserNumber,'time'=>array( 'gt',strtotime(date('Y-m-d'))) );		
		if (self::$db->where($where)->count() >= self::$telephoneSum) {
			self::$error = '当前手机的使用次数已用完,请明日再试';
			return false;
		}

		return true;
	}




}