<?php
Class UserAction extends CommonAction {

    //用户信息主页
    public function index() {
        $userid = getUserID();
        $this->phone = M('Userinfo')->field(array('telephone', 'face'))->where(array('uid' => $userid))->find();
        $this->price = M('Money')->where(array('uid' => $userid))->getField('money');
        $this->display('userinfo');
    }


    //用户安全设置
    public function userinfo() {
        $userid = getUserID();
        $field = array('lock', 'loginip', 'password');
        //读取出用户的信息
       $db=M('Userinfo');
        $this->userinfo = M('User')->field($field, true)->where(array('id' => $userid))->find();
        $this->phone = $db->field(array('telephone', 'face'))->where(array('uid' => $userid))->find();      
        $this->display();
    }

    //用户基本资料
    public function info() {
        $userid = getUserID();
        $user = M('Userinfo')->where(array('uid' => $userid))->find();
        $this->info = $user;
        $this->display();
    }

    //联系人管理
    public function contacter() {
        $userid = getUserID();
        $this->contacter = M('Contacter')->where(array('uid' => $userid))->select();
        $this->display();
    }

    //异步修改联系人
    public function setContacter() {
        if (!$this->isAjax())
            $this->redirect(__APP__);
        //如果cid存在就说明是修改，反之就是添加
        if (!empty(I('post.cid'))) {
            $where = array('id' => I('post.cid', 'intval'));
            //判断有没有修改成功
            if (M('Contacter')->where($where)->save($this->_post())) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            //加入Uid用来识别是哪位用户的联系人
            $_POST['uid'] = getUserID();
            if ($id = M('Contacter')->data($this->_post())->add())
                echo $id;
            else
                echo 0;
        }
    }

    //异步删除联系人
    public function deleteContacter() {
        if (!$this->isAjax())
            $this->redirect(__APP__);
        $where = array('id' => I('post.cid', 'intval'));
        if (M('Contacter')->where($where)->delete())
            echo 1;
        else
            echo 0;
    }

    //设置用户基本资料

    public function setinfo() {
        if (!$this->ispost())
            $this->redirect(__APP__);
        /*
          $user["accountType"] = I("post.accountType");
          $user["trueName"] = I("post.trueName");
          $user["work"] = I("post.work");
          $user["biz"] = I("post.biz");
          $user["website"] = I("post.website",'','htmlspecialchars');
          $user["location"] = I("post.location");
          $user["address"] = I("post.address");
          $user["phone"] = I("post.phone");
          $user["fax"] = I("post.fax");

          $user["UIDx"] = I("post.UIDx");
          $user["ICP"] = I("post.ICP");
          $user["WICP"] = I("post.WICP");
          $user["country"] = I("post.country");
          $user["province"] = I("post.province");
          $user["ZIP"] = I("post.ZIP");
          $user["QQ"] = I("post.QQ");
          $user["ZIPCode"] = I("post.ZIPCode");
         */

        $user = I('post.');
        if (M('Userinfo')->where(array('uid' => getUserID()))->save($user)) {
            $data['status'] = 1;
            $data['message'] = '修改成功';
        } else {
            $data['status'] = 0;
            $data['message'] = '修改失败,您并没有修改内容';
        }
        $this->ajaxReturn($data, 'JSON');
    }

    //设置用户头像
    public function setFace() {
        if (!$this->ispost())
            $this->redirect('Index/index');

        $userinfo = M('Userinfo');
        $where = array('uid' => getUserID());
        //判断用户之前有没有上传自定义头像
        if (!empty($path = $userinfo->where($where)->getField('face')) && strpos($path, '/'))
            $facePath = './Uploads/Face/' . $path;


        //如果face为空就说明是用户自己上传的头像,反之就是选择的个性头像
        if (empty($_POST['face'])) {
            //导入文件上传类
            import('ORG.Net.UploadFile');
            $upload = new UploadFile(); // 实例化上传类
            $upload->maxSize = 2000000; // 设置附件上传大小
            $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->autoSub = true;
            $upload->subType = 'date'; //子目录的存储格式
            $upload->dateFormat = 'Y_m';

            if (!$upload->upload('./Uploads/Face/')) {// 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            } else {// 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
                $face = $info['0']['savename'];


                //如果头像修改成功了
                if (M('Userinfo')->where($where)->save(array('face' => $face))) {

                    //判断用户之前有没有上传头像,如果有就把原来的图片删除
                    if (isset($facePath))
                        unlink($facePath);

                    //跳转
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            //个性头像上传
        }else {
            //判断用户之前有没有上传自定义头像,如果有就把原来的图片删除
            if (isset($facePath))
                unlink($facePath);

            if ($userinfo->where($where)->save(array('face' => $this->_post('face'))))
                echo 1;
            else
                echo 0;
        }
    }

    //用户账户管理主页
    public function account() {
        //读取出用户的金钱
        $money = M('Money')->where(array('uid' => getUserID()))->getField('money');

        $this->price = $money ? $money : '0.00';
        $this->display();
    }

    //我的账户管理界面
    public function recharge() {
        //echo MODULE_NAME;die;
        //echo ACTION_NAME;
        $money = M('Money')->where(array('uid' => getUserID()))->getField('money');

        $this->price = $money ? $money : '0.00';
        $_SESSION['goodsInfo']['number2'] = getNumber();
        $this->display();
    }

    //支付操作
    public function pay() {

        if (!$this->ispost())
            redirect(__APP__);
        if (empty($_POST['price']) || empty($_POST['WIDdefaultbank']))
            redirect(__APP__);

        //导入支付宝验证类
        import('Class.Zhifubao', APP_PATH);

        $zhifubao = new Zhifubao();

        //$zhifubao->creRecharge();
        //跳过支付宝充值
        $zhifubao->creRechargeOutByZhifubao();
        $this->success('充值成功');
    }
    
     //立刻下单
    public function dindan() {
        $userid = getUserID();   
        
        $str=array();
        $hidstr;
        foreach (C('UserDinDan') as $key => $val) {
            
            switch ($key) {
                case 'userID':
                    $value = $userid;
                    break;
                case 'salt':
                    $value = work_order(6);
                    break;
                default:
                    $value = NULL;
                    break;
            }
            $d = mkTableCell($val, $key,$value);
  
            if($d['hid'])
            {
                $hidstr .=$d['ck'];
            }
            else{
                $str[] = $d;
                //$str[]=mkTableCell($val,$key);
            }
        }   
        
        //instrcrcak($userid,3,1);   
        
        $this->contacter=$str;
        $this->hid=$hidstr;
        $this->display();
    }
    
    public function insertdindan() {

        $dd = D('Userdindan');
        if (!$dd->create()) {
            $this->error(array2str($dd->getError()));
        }
        $pst = I('post.');
        $pst['regtime'] = getCurenTime();
        if ($dd->add($pst)) {
            $this->success('添加成功', U('dindanlist'));
        } else {
//            trace($dd->getError());
//            $this->display('dindan');
            $this->error('添加失败');
        }
    }

    public function dindanlist(){        
        $userid = getUserID();
        $this->contacter = D('DindanView')->where(array('userID' => $userid))->select();
        $this->display();
    }

   //修改订单
    public function modifydindan() {
        $userid = getUserID();
        $dindan = D('DindanView')->where(array('id' => I('get.id'), 'salt' => I('get.salt')))->find();
        if ($dindan) {
            $str = array();
            $hidstr;
            foreach (C('UserDinDan') as $key => $val) {
                $d = mkTableCell($val, $key, $dindan[$key]);
                if ($d['hid']) {
                    $hidstr .=$d['ck'];
                } else {
                    $str[] = $d;            
                }
            }
            $this->contacter = $str;
            $this->hid = $hidstr.  mkHiddenInput('id',$dindan['id']);
        }
        $this->display();
    }
    
    //更新订单
    public function updatadindan() {
        $dd = D('Userdindan');
        if (!$dd->create()) {
            $this->error(array2str($dd->getError()));
        }
        $pst= I('post.');
         $where = array('id' => I('post.id', 'intval'),'salt'=>$pst['salt']);
         $pst['moditime'] = getCurenTime();
         unset($pst['id']);
         unset($pst['salt']);
        if ($dd->where($where)->save($pst)) {
               trace($dd->getLastSql());
//            $this->display('modifydindan');
            $this->success('修改成功', U('dindanlist'));
        } else {  
         
            $this->error('添加失败');
        }
    } 
    
    //删除订单
    public function deldindan()
    {
        $userid = getUserID();
        $db=M('userdindan');
        if($db->where(array('id' => I('get.id'), 'salt' => I('get.salt'),'userID'=>$userid,'status'=>0))->delete())
        {
              $this->success('删除成功', U('dindanlist'));
        } else {       
            $this->error('删除失败');
        }      
    }

        //设置用户基本资料

    public function setpass() {
        if (!$this->ispost())
            $this->redirect(__APP__);

        $userid = getUserID();
        $db = M('User');

        /* 验证用户密码 */
        $where = array('id' => $userid);
        $user = $db->field('id,username,password,type,salt,lock')->where($where)->find();
        if ( !$user || $user['type'] != 1 || $user['lock'] == 1 || $user['password'] != md5Salt(I('post.oldPassword'), $user['salt'])) {
            $data['status'] = 0;
            $data['message'] = '原密码错误，请重新输入';
        } else {
            $salt = work_order(6);
            $password = I('post.password');
            $level=password_level($password);
            $save = array(
                'password' => md5Salt($password, $salt),
                'salt' => $salt
            );

            if ($db->where($where)->save($save)) {
                
                $this->instrcrcak($userid,'PWD',$level);   
                
                $data['status'] = 1;
                $data['message'] = '修改成功';
            } else {
                $data['status'] = 0;
                $data['message'] = '修改失败,您并没有修改内容';
            }
        }
        $this->ajaxReturn($data, 'JSON');
    }

     public function setphone() {
        if (!$this->ispost())
            $this->redirect(__APP__);

        $userid = getUserID();
        $db = M('userinfo');

        /* 验证用户原手机 */
        $where = array('uid' => $userid);
        $user = $db->field('id,telephone')->where($where)->find();
        if (!$user || $user['telephone'] != I('post.oldphone')) {
            $data['status'] = 0;
            $data['message'] = '原手机输入错误，请重新输入';
        } else {
      
            $phone = I('post.phone');
            $save = array(
                'telephone' => $phone,     
            );
            if ($db->where($where)->save($save)) {
                 $this->instrcrcak($userid,'PHONE',1);   
                $data['status'] = 1;
                $data['message'] = '修改成功';
            } else {
                $data['status'] = 0;
                $data['message'] = '修改失败,您并没有修改内容';
            }
        }
        $this->ajaxReturn($data, 'JSON');
    }
    
  

    //更新校验属性  更新前会先清除原有记录
  private function instrcrcak($userid, $crcaktype, $crcak=0) {
        $dc = M('user_crcak');
        $ctype=C('CRCAKTYPE.'.$crcaktype);
        $dc->where(array('uid' => $userid,
            'crcaktype' =>$ctype,))->delete();
        $d=array(
            'uid' => $userid,
            'crcaktype' => $ctype,
            'crcak'=>$crcak,
        );
        $dc->add($d);
        return ;
    }

}
