<?php
   Class UserModel extends Model{
        protected $_validate = array(
		
		  array('password','require','密码不能为空',1),
		  array('password','/\S{3,16}/','密码必须在3到16位之间',1),
		  array('dbpassword','require','确认密码不能为空',1),		
		  array('password','dbpassword','两次密码不一致',1,'confirm'), 
          array('username','','帐号名称已经存在！',1,'unique',1), 
          array('telephone','/^1[3|4|5|8][0-9]\d{8}$/','手机格式有误',1),
         // array('verify','require','验证码不能为空',1), //手机短信验证码验证
         // array('verify','verify','验证码有误',1,'callback'), //手机短信验证码验证
          
              
		);
	  
		//	@der 验证短信验证码	    			
	  	protected function verify($verify){
	  		
	        if( $verify != decode($_COOKIE['kz_'.I('post.telephone')]) ){
	  	        return false;
	        }else{
		        return true;
	        }	
                return true;
        }


   
   
   
   
   
   
   }
