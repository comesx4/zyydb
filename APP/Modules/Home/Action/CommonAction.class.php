<?php
Class CommonAction extends Action{
	
	/*
		@der 前置方法
	*/
    public function _initialize(){

    	/*判断用户是否登陆*/
//	    if (getUserID()==0) {
//            $this->redirect('Login/index');
//        }
        checkLogin();

        /*判断用户类型，如果不是类型1，则清空会话信息*/	    
        if (I('session.' . C('SAFE.TYPEHAND')) != 1) {
            $_SESSION = array();
    		if(isset($_COOKIE[session_name()])){
		    	setcookie(session_name(),'',time()-3600,'/');		  	 
			}
    		session_destroy();

    		$this->redirect('Login/index');	      
    	}
    }
   
}
