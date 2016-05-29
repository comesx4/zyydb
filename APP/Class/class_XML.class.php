<?php
class XMLS{

	//创建XML
	public static function create($root,array $arr){
		$dom=new DOMDocument('1.0','UTF-8');
		$dom->appendChild(self::createXMLElement($dom,$root,$arr));
		
		return $dom->saveXML();	
	}//create()
	
	//创建XML元素
	private static function createXMLElement(DOMDocument $dom,$name,array $arr){
		$element=$dom->createElement($name);
		
		$newArr=array();
	
		foreach($arr as $key=>$value){
			if(is_array($value)){
				
				foreach($value as $k=>$v){
					if(is_integer($k)){
						$newArr[$key.'.'.$k]=$v;
					}else{
						$newArr[$key]=$value;
						break;
					}//if
				}//foreach as
				
			}else{
				$newArr[$key]=$value;	
			}//if	
		}//foreach as
		
		foreach($newArr as $key=>$value){
			if(is_array($value)){
				$arr=explode('.',$key);
				$element->appendChild(self::createXMLElement($dom,$arr[0],$value));
			}else{
				$element->appendChild($dom->createElement($key,$value));
			}//if	
		}//foreach as
		
		return $element;
	}//createXMLElement()
	
	

	private function __construct(){}//__construct()
	
}//XML
?>