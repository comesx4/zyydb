<?php
Class LoginAction extends Action{
	
	/*
		@der 前置操作
	*/
	public function _before_index(){		
    	//如果用户已经登录了进入这个控制器就会返回主页
		if(!empty($_SESSION['id']))
			$this->redirect('Index/index');
  	}
	
    /*
		@der 登陆页面
    */
	public function index(){	 
	    $_SESSION['HTTP_REFERER']=$_SERVER['HTTP_REFERER'];
		$this->display();
	}

    /*
		@der 登陆操作
    */
    public function dologin(){	
	    if(!$this->ispost()) redirect(__APP__);

	    //验证码验证
	    if( $_POST['code'] != strtolower($_SESSION['code']) ) {
	    	redirect(U('Login/index',array('username' => $where['username'] , 'code_type' => 2)));
	    }
	    unset($_SESSION['code']);
	    
	    $db = M('User');
	    /*验证用户名和密码*/
   	 	$user = $db->field('id,username,password,type,salt')->where(array('username'=>I('post.username')))->find();
   	 	if ( !$user || $user['password'] != md5Salt(I('post.password'),$user['salt']) ) {
   	 		redirect(U('Login/index',array('username' => $where['username'] , 'code_type' => 1)));
   	 	}

   	 	/*更新登陆时间*/
	    $save=array(
	        'logintime'=>$_SERVER['REQUEST_TIME'],
	        'loginip'=>get_client_ip()
	    );	    
	    $db->where(array('id'=>$user['id']))->save($save);		       
        
        /*用户信息表*/
        $userinfo = M('Userinfo')->field('telephone')->where(array('uid'=>$user['id']))->find();

        $_SESSION['userinfo'] = $userinfo;
        $_SESSION['uname']    = $user['username'];
        $_SESSION['id']	      = $user['id'];
        $_SESSION['userType'] = $user['type'];
        if ($user['type'] == 1) {
        	$_SESSION['username'] = $user['username'];
        }

	    if (empty($_SESSION['HTTP_REFERER'])) {
	        redirect(U('Console/Index/index'));
	    } else {
	        redirect($_SESSION['HTTP_REFERER']);
	    }
    }

  	/*
		@der 退出登录
  	*/
    public function loginOut(){
        $_SESSION=array();		   
        if(isset($_COOKIE[session_name()])){
		    setcookie(session_name(),'',time()-3600,'/');		  	 
		}
  		session_destroy();
  		redirect(__APP__);
    }
    
}
