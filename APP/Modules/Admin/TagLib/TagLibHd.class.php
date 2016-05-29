	<?php  
class Box {
    public function shuzi($val) {        
      return $val;
    }
    
    public function longtoIP($val){
        trace($val);
           $ip_long =$val;
            $ip1 = ($ip_long >> 24) & 0xff; // 跟0xff做与运算的目的是取低8位
            $ip2 = ($ip_long >> 16) & 0xff;
            $ip3 = ($ip_long >> 8) & 0xff;
            $ip4 = $ip_long & 0xff;         
            $sip =  $ip1 . '.' . $ip2 . '.' . $ip3 . '.' . $ip4 . '<br/>';
            return $sip;
            
            
    }
} 
  
  Class TagLibHd extends TagLib{
   protected $tags=array(   
         'userinfo'=>array('attr'=>'limit','close'=>1),
        'userinfoip'=>array('attr'=>'type,name,id,value','close'=>1), // input标签
       
   );
     //用户个人信息
   public function _userinfo($attr,$content){
	   $attr=$this->parseXmlAttr($attr);
            $ip_long = $attr['limit'];
            $ip1 = ($ip_long >> 24) & 0xff; // 跟0xff做与运算的目的是取低8位
            $ip2 = ($ip_long >> 16) & 0xff;
            $ip3 = ($ip_long >> 8) & 0xff;
            $ip4 = $ip_long & 0xff;
//          echo $ip1 . '.' . $ip2 . '.' . $ip3 . '.' . $ip4 . "\n";           
            $sip =  $ip1 . '.' . $ip2 . '.' . $ip3 . '.' . $ip4;
	    $str .=$sip;//$content;//$attr['limit']."dd";       
	    return $str;
   }
   
       public function _userinfoip($attr,$content)   {
            $tag    = $this->parseXmlAttr($attr);
            $name   =   $tag['name'];
            $id    =    $tag['id'];
            $type   =   $tag['type'];
            $value   =   $tag['value'];            
        $box = new Box();                
        $code = <<<END
        {$box->shuzi("<?php echo" .  $value ."; ?>")}
END;
            return $code;
        }
        
           public function _userinfoipx($attr,$content)   {
            $tag    = $this->parseXmlAttr($attr);
            $name   =   $tag['name'];
            $id    =    $tag['id'];
            $type   =   $tag['type'];
            $value   =   $tag['value'];
            //$value = $this->autoBuildVar($tag['value']);
              
            $ip_long =$value;
            $ip1 = ($ip_long >> 24) & 0xff; // 跟0xff做与运算的目的是取低8位
            $ip2 = ($ip_long >> 16) & 0xff;
            $ip3 = ($ip_long >> 8) & 0xff;
            $ip4 = $ip_long & 0xff;         
            $sip =  $ip1 . '.' . $ip2 . '.' . $ip3 . '.' . $ip4 . '<br/>';
            
            $str = "<literal type='".$type."' id='".$id."' name='".$name."'><?php echo ";
            $str .=  $value ."; ?></literal>";
            return $str;
        }
   
  }
            