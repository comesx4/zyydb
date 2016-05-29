<?php
/*
	@der 用于处理xml数据
*/
Class Xml{
	//是否带着xml头信息  
	private static $isHeader = true;

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
		@der 创建xml数据
		@param array $data xml数据
		@return xml
	*/
	public static function createXml($data){
		if (!is_array($data)) {
			return false;
		}
		$xml = self::xml_merge($data);
		if (self::$isHeader) {
			$xml = '<?xml version="1.0" encoding="utf-8"?>'.$xml;
		}
		
		return $xml;
	}

	/*
		@der 解析xml数据
		@param xml $data
		@param boolean $isarray 返回数据类型 true(array) false(object) 
		@reutrn object || array
	*/
	public static function xml_convert($data , $isarray = false){
		//解析XML数据
		$data = simplexml_load_string($data,'SimpleXMLElement',LIBXML_NOCDATA);
		if ($returnType == 'array') {
			$data = (array)$data;
		}
		return $data;
	}
		
	/*
		@der 将数组转换成xml
		@reutrn xml
	*/
	private static function xml_merge($data){
		$xml = '';
		foreach ($data as $key => $value) {
			if (is_array($data[$key])) {				
				/*回调*/
				$xml .= "<{$key}>".self::xml_merge($data[$key])."</{$key}>";
			} else {
				$xml .= "<{$key}>{$value}</$key>";
			}
		}

		return $xml;
	}

	/*
		@der 禁止new 对象
	*/
	private function __construct(){}

}