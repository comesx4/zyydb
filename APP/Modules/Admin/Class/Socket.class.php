<?php

/*
	@der 处理socket请求

	 +-------------------------------
	 *    @socket连接整个过程
	 +-------------------------------
	 *    @socket_create
	 *    @socket_connect
	 *    @socket_write
	 *    @socket_read
	 *    @socket_close
	 +--------------------------------
*/
Class Socket{
	//socket连接资源
	private static $socket;
	//连接的IP
	private static $ip    = '43.229.108.2';
	//端口
	private static $port  = '35627';
	//记录错误消息
	private static $errorMsg;
	//返回的数据
	private static $data;
	//超时时间
	private static $time_limit = 22;
	//状态码
	private static $code = 0;

	/*
		@der 给成员设置属性的方法
		@param string $key 成员名称		
		@param string $value 值
	*/
	public static function set($key,$value){
		if ( isset(get_class_vars(get_class())[$key]) ) {
			self::$$key = $value;
		}
	}

	/*
		@der 发送请求
		@der xml $data 发送数据	
		@param $in socket_write()函数的第三个参数
		@param string $server_ip 连接的IP
		@param int $server_ip 连接的端口
		return mixed || boolean 
	*/
	public static function send($data ,$in = '',$server_ip = '', $server_port = ''){
		$server_ip   && self::$server_ip   = $server_ip;
		$server_port && self::$server_port = $server_port;

		//建立连接
		if ( !self::create_socket() ) {
			return false;
		}

		if ( !self::send_data($data ,$in) ) {
			return false;
		}
		socket_close(self::$socket);

		return self::$data;
	}	

	/*
		@der 获取错误消息
		@return string 
	*/
	public static function getErrorMsg(){
		return self::$errorMsg;
	}

	/*
		@der 获取状态码
	*/
	public static function getCode(){
		return self::$code;
	}

	/*
		@der 创建socket连接
		@return boolean
	*/
	private static function create_socket(){
		// 设置超时时间
		set_time_limit(self::$time_limit);

		/*创建socket资源*/
		self::$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if (self::$socket < 0) {
			self::$code = '400';
		    self::$errorMsg = 'socket_create() failed. '.socket_strerror(socket_last_error(self::$socket));
		    return false;
		}

		/*进行连接*/
		$result = socket_connect(self::$socket, self::$ip, self::$port);
	
		if ($result < 0) {
			self::$code = '400';
		    self::$errorMsg = 'socket_connect() failed. '.socket_strerror(socket_last_error(self::$socket));
		    return false;
		}

		return true;
	}

	/*
		@der 发送数据
		@der xml $data 发送数据	
		@return mixed
	*/
	private static function send_data($data,$in = ''){
		
		if(!socket_write(self::$socket, $data, $in)) {
			self::$code = '400';
		    self::$errorMsg = 'socket_write() failed. '.socket_strerror(socket_last_error(self::$socket));
		    return false;
		}

		/*老大写的，直接复制过来的*/
		$data = @socket_read(self::$socket,4);
		$data = @unpack("N",$data);

		$length = $data[1];
		
		$document = @socket_read(self::$socket,$length);
	
		while(strlen($document) != $length-4) {
			
			$document .= @socket_read(self::$socket,$length);	
		}//while

		self::$data = $document;

		return true;
	} 

}