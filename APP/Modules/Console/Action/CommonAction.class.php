<?php
Class CommonAction extends Action{
    
    /*
		@der 第一个调用的方法
    */
    public function _initialize(){    	

    	/*判断用户是否登陆*/
        if(empty($_SESSION['id'])){
            $this->redirect('Login/index');
        }

        /*如果用户是从首页那边过来的*/
        if (empty($_SESSION['uname'])) {
        	$_SESSION['uname'] = $_SESSION['username'];
        }

    }
}

