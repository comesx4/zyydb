<?php

/*
  用户工单管理控制器
 */

Class WoAction extends CommonAction {
    /*
      用户工单列表
     */

    public function index() {
        //echo uniqid();die;
        $db = D('WoView');
        //查找出用户所在的用户组
        $group = M('Role_user')->field('role_id')->where(array('user_id' => $_SESSION['uid']))->select();
//           trace($group,'group1');
        $group = implode(',', peatarr($group, 'role_id'));
//   trace($group,'group2');
        //查找出该组对应的工单分类  
        $cid = M('Scencecat_role')->field('sid')->where(array('rid' => array('in', $group)))->select();
        $cid = implode(',', array_unique(peatarr($cid, 'sid')));
        trace($cid, 'cid');
        //根据分类ID查找对应的且没有被转过的工单
        $where = array('cid' => array('in', $cid), 'status' => array('neq', 3), 'isturn' => 0, 'del' => 0);
        //$where=array('status'=>array('neq',3),'isturn'=>0,'del'=>0);
        //调用Model中的方法处理搜索
        list($where, $pwhere, $tmp) = $db->serach($where);
        trace($where);
        //导入分页类
        $page = X($db, $where, 8, $pwhere);
        $this->data = $db->where($where)->order('isCharge DESC,complaint DESC,status ASC')->limit($page['0']->limit)->select();

        //查找出转发过来的工单
        $wid = M('Wo_role')->field('wid')->where(array('rid' => array('in', $group)))->select();
        $wid = implode(',', peatarr($wid, 'wid'));
        $this->turn = $db->where(array('id' => array('in', $wid), 'status' => array('neq', 3)))->order('complaint DESC,status ASC')->select();
        $this->tmp = $tmp;
        $this->fpage = $page['1'];
        $this->display();
    }

    /*
      用户工单详细页面
     */

    public function details() {
        if (empty($_GET['id']))
            redirect(__APP__);
        $db = D('Woreply');
        $id = I('get.id');
        //调用Model中的方法获取详细信息
        $wo = $db->get_info($this);

        //查找出工单记录

        if ($wo['orderid']) {
            $this->showorder = true;
            $datax = D('GoodsorderView')->field(true)->where(array('id' => $wo['orderid']))->find(); //订单明细   
            trace($datax);
            $this->oinfo = $datax;
            $this->roomlist = show_list('City', 'roomid', 'city', $datax['roomid']);
            $this->status = FLFParameter::getStatusList('svstatus', 'StatusList', $datax['svstatus']);
        }

        $this->record = D('WoRecordView')->where(array('wid' => $id))->select();

        //查找出该工单所属用户组
        if ($wo['isturn'] == 1) {
            //工单被转部门的情况
            $group = M('Wo_role')->field('rid')->where(array('wid' => $id))->select();
            $group = peatarr($group, 'rid');
        } else {
            //工单没有转部门的情况
            $group = M('Scencecat_role')->field('rid')->where(array('sid' => $wo['cid']))->select();
            $group = peatarr($group, 'rid');
        }

        //查找出用户购买的服务信息
        $this->service = D('User_serviceView')->where(array('uid' => $wo['uid'], 'status' => 0))->select();

        //复选框
        $this->checkbox = show_checkbox('Role', 'rid', 'name', $group);

        $this->display();
    }

    /*
      添加记录的操作
     */

    public function add_record() {
        if (!$this->ispost())
            redirect(__APP__);
        $pst = I('post.');
        $pst['time'] = time();
        $pst['aid'] = getAdminID();

        if (M('Wo_record')->add($pst)) {
            $this->success('记录添加成功', U('index'));
        } else {
            $this->error('添加失败，请重试');
        }
    }

    /*
      工单转部门的操作
     */

    public function turn() {
        if (!$this->ispost())
            redirect(__APP__);
        $db = M('Wo_role');
        $wid = $this->_post('wid', 'intval');

        //将工单修改成转部门状态
        M('Wo')->where(array('id' => $wid))->setField(array('isturn' => 1));
        //删除原有的转部门关系
        $db->where(array('wid' => $wid))->delete();
        //添加新的转部门关系
        $add = array();
        $name = '';
        foreach ($_POST['rid'] as $v) {
            $add[] = array('wid' => $wid, 'rid' => $v);
            //查找出组名称
            $name .= M('Role')->where(array('id' => $v))->getField('name') . ',';
        }
        $name = rtrim($name, ',');

        if ($db->addAll($add)) {

            //生成工单记录
            create_record(M('Wo_record'), "{$_SESSION['username']}将工单转交给({$name})", array('wid' => $wid));
            $this->success('转部门成功', U('index'));
        } else {
            $this->error('转部门失败，请重试');
        }
    }

    /*
      工单的回复操作
     */

    public function reply() {
        if (!$this->ispost())
            redirect(__APP__);
        $pst = I('post.');
        $pst['time'] = time();
        $pst['type'] = getAdminID();
        $status = isset($pst['status']) ? $pst['status'] : 1;
        $end = isset($pst['status']) ? time() : 0;


        if ($pst['orderid']) { //修改订单状态
            $order = array();
            $order['roomid'] = $pst['roomid'];
            $order['cabinetID'] = $pst['cabinetID'];
            $order['svstatus'] = $pst['svstatus'];
            $order['IP'] = $pst['IP'];
            $order['startday'] = strtotime($pst['min-date']);
            $order['endday'] = strtotime($pst['max-date']);
            $order['id'] = $pst['orderid'];
            switch ($pst['svstatus']) {
                case C('StatusList.正在施工'):
                    $order['orderstatus'] = C('OrderStatus.正在施工');
                    break;
                case C('StatusList.正常服务'):
                    $order['orderstatus'] = C('OrderStatus.施工完毕');
                    break;
            }

            $detail = '机房：';
            $detail.=$pst['roomid'];
            $detail.='|服务状态：';
            $detail.=getkeyname($pst['svstatus'], 'StatusList');
            $detail.='|机柜号：';
            $detail.=$pst['cabinetID'];
            $detail.='|IP：';
            $detail.=$pst['IP'];
            $detail.='|起始时间：';
            $detail.=$pst['min-date'];
            $detail.='|结束时间：';
            $detail.=$pst['max-date'];
            FLFParameter::ADD_OrderLog('orderlog', array(
                'orderID' => $pst['orderid'],
                'adminID' => getAdminID(),
                'userOp' => 3,
                'opdetail' => $detail,
                'optime' => time(),
                'opIP' => get_client_ip(0),
            ));

            M('goodsorder')->save($order);
        }

        $reply = array();
        $reply['time'] = $pst['time'];
        $reply['type'] = $pst['type'];
        $reply['title'] = $pst['title'];
        $reply['wid'] = $pst['wid'];

        //添加回复
        if (M('Woreply')->add($reply)) {

            $db = M('Wo');
            $tb = M('Wo_record');
            $where = array('id' => $pst['wid']);

            $set = array('status' => $status, 'aid' => getAdminID(), 'end' => $end);
            //修改工单状态
            $db->where($where)->setField($set);

            //判断是否将状态改成待处理了
            if (isset($pst['status'])) {
                //判断该工单的记录中是否有客服回复
                if (!$tb->where(array('wid' => $pst['wid'], 'type' => 2))->getField('id')) {
                    create_record($tb, "{$_SESSION['username']}回复了该工单", array('wid' => $pst['wid'], 'type' => 2));
                }

                create_record($tb, "{$_SESSION['username']}回复了该工单，并将状态改成了待处理", array('wid' => $pst['wid'], 'type' => 3));
            } else {

                //判断该工单的记录中是否有客服回复
                if ($tb->where(array('wid' => $_POST['wid'], 'type' => 2))->getField('id')) {

                    create_record($tb, "{$_SESSION['username']}回复了该工单", array('wid' => $pst['wid']));
                } else {

                    create_record($tb, "{$_SESSION['username']}回复了该工单", array('wid' => $pst['wid'], 'type' => 2));
                }
            }


            //发送邮件通知
            if (isset($pst['email'])) {

                //查找出工单信息
                $info = $db->field('number,email')->where($where)->find();
                if (isset($pst['status'])) {
                    $title = "阿里云售后支持工单(ID：{$info['number']})待确认提醒"; //邮件标题                    
                    $content = "您的工单处于待确认，请您注意查看<a href='http://127.0.01/aliyun/index.php/scence/scenceInfo/id/{$_POST['wid']}.html'>点击查看</a>"; //邮件内容
                } else {
                    $title = "阿里云售后支持工单(ID：{$info['number']})待反馈提醒"; //邮件标题   
                    $content = "<html>
                    <body>
                    您的工单处于待反馈，请您注意查看<a href='http://127.0.01/aliyun/index.php/scence/scenceInfo/id/{$pst['wid']}.html'>点击查看</a>
                    </body>
                    </html>"; //邮件内容
                }

                //发送邮件
                send_email($info['email'], $title, $content);
            }

            $this->success('回复成功', U('index'));
        } else {

            $this->error('回复失败，请重试');
        }
    }

    /*
      @der 工单的回收站视图
     */

    public function recycle() {
        $db = D('WoView');
        $where = array('del' => 1);
        //调用Model中的方法处理搜索
        list($where, $pwhere, $tmp) = $db->serach($where);
        //导入分页类
        $page = X($db, $where, 8, $pwhere);
        $this->data = $db->where($where)->order('isCharge DESC,complaint DESC,status ASC')->limit($page['0']->limit)->select();

        $this->tmp = $tmp;
        $this->fpage = $page['1'];
        $this->display();
    }

    /*
      @der 工单放入回收站和恢复的操作
     */

    public function introduction_recycle() {
        $id = $this->_get('id', 'intval');
        $setField = isset($_GET['bin']) ? array('del' => 0) : array('del' => 1);

        if (M('Wo')->where(array('id' => $id))->setField($setField)) {
            $this->success('操作成功', $_SERVER['HTTP_REFERER']);
        } else {
            $this->error('操作失败,请重试');
        }
    }

    /*
      工单的删除操作
     */

    public function del() {
        $id = $this->_get('id', 'intval');

        //先删除工单
        if (M('Wo')->where(array('id' => $id))->delete()) {

            D('Woreply')->del($id);

            $this->success('删除成功', U('index'));
        } else {
            $this->error('删除失败');
        }
    }

    /*
      修改平均时间的页面

     */

    public function avg() {

        $this->display();
    }

    /*
      计算工单平均时间的方法
     */

    public function set_avg() {
        if (!$this->ispost())
            redirect(__APP__);

        $type = $this->_post('type', 'intval');
        $time = $this->_post('time', 'intval') * 3600;

        if (get_avg($type, $time)) {
            $this->success('时间计算成功', U('index'));
        } else {
            $this->error('计算失败，请重试！');
        }
    }

}
