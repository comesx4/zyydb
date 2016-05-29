<?php

Class LoginAction extends Action {

    //前置操作
    public function _before_index() {

        //如果用户已经登录了进入这个控制器就会返回主页
        if (!empty($_SESSION['id']))
            $this->redirect('Index/index');
    }

    //登陆页面
    public function index() {
        $_SESSION['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
        $this->display();
    }

    //注册页面
    public function register() {

        $this->display('register2');
    }

    
    //验证用户名是否存在
    public function checkusername() {
        if (!$this->ispost()) {
            halt('页面不存在');
        }
        if (M('user')->where(array('username' => $_POST['username']))->count() > 0)
            echo 'false';
        else
            echo 'true';
    }

    //验证码
    public function code() {
        import('Class.Image', APP_PATH);
        Image::verify(4, 150, 30);
    }

    //ajax验证验证码
    public function checkcode() {
        if (!$this->isajax())
            halt('页面不存在');
           Log::write('调试的登录IP：'. C('WEBSITE_SET')['VERIFY_ENABLED'], Log::INFO);
        if ( C('WEBSITE_SET')['VERIFY_ENABLED']) {
            if (strcasecmp(I('post.code'), I('session.code')) == 0)
                echo 'true';
            else
                echo 'false';
        }
        else {
            echo 'true';
        }
    }

    /*
      @der 注册表单的数据插入
     */

    public function insert() {
        if (!$this->ispost())
            redirect(__APP__);
        $user = D('User');

        /* 自动验证 */
        if (!$user->create()) {
            $this->error($user->getError());
        }

        $salt = work_order(6);
        $add = array(
            'username' => I('post.username', ''),
            'password' => md5Salt(I('post.password'), $salt),
            'salt' => $salt,
            'register' => $_SERVER['REQUEST_TIME']
        );

        if ($id = $user->add($add)) {
            //用户信息表
            M('Userinfo')->add(array('telephone' => I('telephone', ''), 'uid' => $id));
            //生成用户的账户表记录
            M('Money')->add(array('uid' => $id));
            $this->success('注册成功', U('login/index',array('username'=>I('post.username', ''))));
        } else {
            $this->error('注册失败');
        }
    }

    public function insert2() {
        if (!$this->ispost())
            redirect(__APP__);
        $user = D('User');
        
        
        /* 自动验证 */
         if (!$user->create()) {
             $this->error($user->getError());
         }

        /* 清空这个账户的session */
        $username = mymd5(I('post.username'));
        $_SESSION[$username] = null;
        
        $salt = work_order(6);
        $add = array(
            'username' => I('post.username', ''),
            'password' => md5Salt(I('post.password'), $salt),
            'salt' => $salt,
            'register' => $_SERVER['REQUEST_TIME']
        );        
 

          if ($id = $user->add($add)) {
            //用户信息表
            M('Userinfo')->add(array('telephone' => I('telephone', ''), 'uid' => $id));
            //生成用户的账户表记录
            M('Money')->add(array('uid' => $id));
            echo '注册成功';
           // $this->success('注册成功', U('index'));
        } else        { 
            //$this->error('注册失败');
             echo '注册失败';
            }
       trace($id, '用户id');
        
            $this->display();
    }

    /*
      @der 登陆操作
     */

    public function dologin() {
        if (!$this->ispost())
            halt('页面不存在');
        $db = M('User');
        //验证码验证
          if ( C('WEBSITE_SET')['VERIFY_ENABLED']) {
            if (strcasecmp(I('post.code'), I('session.code'))!== 0)
            {
                redirect(U('Login/index', array('username' => $where['username'], 'code_type' => 2)));             
            }
        }
        unset($_SESSION['code']);

        /* 验证用户名和密码 */
        $where = array('username' => I('post.username'));
        $user = $db->field('id,username,password,type,salt,lock')->where($where)->find();
        if (!$user || $user['type'] != 1|| $user['lock'] == 1 || $user['password'] != md5Salt(I('post.password'), $user['salt'])) {
            redirect(U('Login/index', array('username' => $where['username'], 'code_type' => 1)));
        }
        
        Log::write('调试的登录IP：'. get_client_ip(1), Log::INFO);
        /* 更新登陆时间 */
        $save = array(
            'logintime' => $_SERVER['REQUEST_TIME'],
            'loginip' => get_client_ip(0)

        );
        $db->where(array('id' => $user['id']))->save($save);
        /* 用户信息表 */
        $userinfo = M('Userinfo')->field('telephone')->where(array('uid' => $user['id']))->find();

        session(C('SAFE.INFOHAND'),$userinfo);
        session(C('SAFE.NAMEHAND'),$user['username']);
        session(C('SAFE.IDHAND'),$user['id']);
        session(C('SAFE.TYPEHAND'), $user['type']);
        /*
        $_SESSION['userinfo'] = $userinfo;

        $_SESSION['username'] = $user['username'];
        $_SESSION['id'] = $user['id'];
        $_SESSION['userType'] = $user['type'];*/

        if (empty($_SESSION['HTTP_REFERER'])) {
            $this->redirect('Index/index');
        } else {
            redirect($_SESSION['HTTP_REFERER']);
        }
    }

    //设置用户名信息界面
    public function setuserinfo() {
        if (!$this->isget())
            halt('页面不存在');

        //判断用户是不是按正常流程走过来的
        if (empty($_SESSION[$_GET['username']])) {
            $this->redirect('Index/index');
        }        
        
        $_GET['username'] = mymd5($_GET['username'], 1);
        $this->user = $_GET;

        $this->display();
    }

    //邮件发送操作
    public function sendmail() {
        if (!$this->ispost())
            halt('页面不存在');
        header("content-type;charset=utf8");
        //判断验证码是否错误
        if ($this->_post('code') != $_SESSION['code'])
            $this->error('验证码有错误');

        //判断用户邮箱是否存在
        if (M('user')->where(array('username' => $this->_post['username']))->count() > 0)
            $this->error('邮箱已存在');

        //调用加密函数加密邮箱，并将它存储到session中
        $username = mymd5($this->_post('username'));
        $_SESSION[$username] = $username;


        $smtpemailto = $this->_post('username'); //发送给谁
        $mailsubject = "继续完成您的智御云计算账号注册"; //邮件主题

        $website = C('WEBSITE_SET');
        
       $urls = 'http://';
       $urls .=$website['SITE_DOMAIN'];
       $urls .= U('setuserinfo', 'username='.$username);
       $mailbody = "<html>
        <head></head>
        <body>    
  <a href='{$urls}'>点击完成注册</a>
        或者复制下面的网址完成注册：{$urls}
  </body></html>"; //邮件内容 
        send_mail($smtpemailto, $mailsubject, $mailbody); 
              
        $this->redirect('Login/email?username=' . $username . '');
    }

    //接收邮件页面
    public function email() {
        if (!$this->isget())
            halt('页面不存在');

        //判断用户是不是按正常流程走过来的
        if (empty($_SESSION[$_GET['username']])) {
            $this->redirect('Index/index');
        }

        //解密用户邮箱
        $username = mymd5($this->_get(username), 1);

        //截取服务商 如123@qq.com 截取qq
        $arr = explode('.', explode('@', $username)['1'])['0'];

        //根据取出来的值来判断邮箱的链接
        $str = "http://mail.{$arr}.com";
        $this->href = $str;

        $this->username = $username;

        $this->display();
    }

    /*
      @der 发送短信验证码
     */

    public function sendInformation() {
        if (!$this->isajax())
            redirect(__APP__);
        $telephone = I('post.telephone');
        //验证手机格式
        if (!preg_match('/^1[3|4|5|8][0-9]\d{8}$/is', $telephone)) {
            echo json_encode(array('status' => 0, 'message' => '手机格式有误'));
        }
        /* 导入短信发送类 */
        import('Class.Telephone', APP_PATH);
        if (!$message = Telephone::sendInformation($telephone)) {
            echo json_encode(array('status' => 1));
        } else {
            echo json_encode(array('status' => 0, 'message' => $message));
        }
    }

    /*
      @der 验证短信验证码
     */

    public function checkVerify() {
        if (!$this->isajax())
            redirect(__APP__);
        
        //禁止短信验证
        echo 'true';
        
        /*
        if (I('post.verify') == decode($_COOKIE['kz_' . I('post.telephone')])) {
            echo 'true';
        } else {
            echo 'false';
        }*/
    }

    //退出登录
    public function loginOut() {
        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();
        $this->success();
        //$this->redirect('index');
    }

}
