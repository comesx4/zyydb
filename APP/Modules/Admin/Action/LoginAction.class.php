<?php

Class LoginAction extends Action {

    //登陆页面
    public function index() {
        $this->display();
    }

    /**
     * 登录操作处理
     */
    Public function login() {
        if (!$this->isPost()) {
            halt('页面不存在');
        }

        if (!isset($_POST['submit'])) {
            return false;
        }

//      验证码对比
        if ($_SESSION['verify'] != md5($_POST['verify'])) {
            $this->error('验证码错误');
        }

        $map = array();
        $map['username'] = I('post.uname');
        
        import('ORG.Util.RBAC');
        //读取用户资料
        $authInfo = RBAC::authenticate($map);

        if (empty($authInfo)) {
            $this->error('账号不存在或者被禁用!');
        } else {
            if ($authInfo['password'] != md5Salt(I('post.pwd'), $authInfo['salt'])) {
                $this->error('账号密码错误!');
            } else {
                if ($authInfo['lock']) {
                    $this->error('账号被锁定');
                }
            }
        }


        //修改最后一次登录的时间和IP
        $model =  C('USER_AUTH_MODEL'); 
        $db = D($model);
        $data = array(
            'id' => $authInfo['id'],
            'logintime' => time(),
            'loginip' => get_client_ip()
        );
        $db->save($data);

        //将用户识别的认证号放到session中,值是登陆用户的ID
        $_SESSION[C('USER_AUTH_KEY')] = $authInfo['id'];
        //超级管理员识别
        if ($authInfo['username'] == C('RBAC_SUPERADMIN'))
            session(C('ADMIN_AUTH_KEY'), true);
        else
            session(C('ADMIN_AUTH_KEY'), false);

        //导入RBAC类
        import('ORG.Util.RBAC');
        //读取用户权限
        RBAC::saveAccessList();     
        session('username', $authInfo['username']);
        session('logintime', date('Y-m-d H:i', $authInfo['logintime']));
        session('now', date('Y-m-d H:i', time()));
        session('loginip', $authInfo['loginip']);
        session('admin', $authInfo['admin']);
        $this->success('正在登录...', U('Index/index'));

    }

    function checklogin() {
        //此处多余可自行改为Model自动验证    
        $map = array();
        $map['username'] = $_POST['username'];
        $map['status'] = array('gt', 0);
        if ($_SESSION['verify'] != md5($_POST['verify'])) {
            $this->error('验证码错误！');
        }

        import('ORG.Util.RBAC');
        //C('USER_AUTH_MODEL','User');
        //验证账号密码
        $authInfo = RBAC::authenticate($map);

        if (empty($authInfo)) {
            $this->error('账号不存在或者被禁用!');
            die;
        }

        if ($authInfo['password'] != md5($_POST['password'])) {
            $this->error('账号密码错误!');
            die;
        }


        session(C('USER_AUTH_KEY'), $authInfo['id']); //记录认证标记，必须有。其他信息根据情况取用。
        session('email', $authInfo['email']);
        session('nickname', $authInfo['nickname']);
        session('user', $authInfo['username']);
        session('last_login_date', $authInfo['last_login_date']);
        session('last_login_ip', $authInfo['last_login_ip']);
        //判断是否为超级管理员
        if ($authInfo['username'] == 'admin') {
            session(C('ADMIN_AUTH_KEY'), true);
        }
        //以下操作为记录本次登录信息
        $user = M('User');
        $lastdate = date('Y-m-d H:i:s');
        $data = array();
        $data['id'] = $authInfo['id'];
        $data['last_login_date'] = $lastdate;
        $data['last_login_ip'] = $_SERVER["REMOTE_ADDR"];
        $user->save($data);
        RBAC::saveAccessList(); //用于检测用户权限的方法,并保存到Session中
        $this->success('登录成功!');
    }

    /**
     * 验证码
     */
    Public function verify() {
        import('ORG.Util.Image');
        Image::buildImageVerify();
    }

    //退出登录
    public function loginOut() {
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        if (isset($_COOKIE['users'])) {
            setcookie('users', '', time() - 3600, '/');
        }

        session_destroy();

        $this->redirect('index');
    }

}
