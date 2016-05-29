<?php

//订单管理控制器
Class OrderAction extends CommonAction {

    //订单列表
    public function index() {
        $t = I('get.');
        $gid = I('get.gid', 1);
        $where['gid'] = $gid;
        if (!empty($t['number']))
            $where['number'] = array('like', '%' . trim($t['number']) . '%');
        if (!empty($t['xname']))
            $where['username'] = array('like', '%' . trim($t['xname']) . '%');

        if (empty($t['max-date']))
            $where['register'] = array(array('EGT', strtotime($t['min-date'])), array('ELT', time()), 'AND');
        else
            $where['register'] = array(array('EGT', strtotime($t['min-date'])), array('ELT', strtotime($t['max-date'])), 'AND');

        $this->goodslista = show_lista('goods', 'gid', 'goods', 'btn', array('gid' => $gid));
        $db = D('Goodsorder_' . $gid . 'View');
        $page = X($db, $where, '', getUrlParameter($t));
        $data = $db->where($where)->scope('orderby')->limit($page['0']->limit)->select();
        $this->order = $data;
        $this->fpage = $page['1'];
        $this->display();
    }

    public function update() {
        $id = I('get.id', 0, 'intval');
        $gid = I('get.gid', 0, 'intval');
        $data = D('GoodsorderView')->where(array('id' => $id))->find(); //订单明细     
//        $this->roomlist = show_list('City', 'roomid', 'city', $data['roomid']);
        $this->oinfo = $data;
        $this->ostatus = C('OrderStatus');
        $this->olog = D('OrderlogView')->where(array('orderID' => $id))->scope('orderby')->select(); //日志
        $this->row = D(C("view_goods{$gid}"))->field(true)->where(array('id' => $data['pid']))->find(); //产品明细
        $this->odtype = FLFParameter::getStatusList('orderType', 'OrderType', $datax['orderType'], 1);
        $where = array(
            'orderid' => $id,
        );
        $ws = D('WoView');
        $wols = $ws->where($where)->order('isCharge DESC,complaint DESC,status ASC')->select(); //工单列表
        trace($ws->getLastSql());
        trace($wols);
        $this->wol = $wols;
        $this->showwo = true;
        $this->display();
    }

    public function save() {

        if (!$this->ispost())
            $this->redirect('Index/index');
        $id = I('post.id', 0, 'intval');
        $ostat = C('OrderStatus');
        $data = M('goodsorder')->field(true)->where(array('id' => $id))->find(); //订单明细  
        $gid = $data['gid'];


        $detail = '修改订单状态：';
        $detail.=getkeyname($data['orderstatus'], 'OrderStatus');
        $detail.='-->';
        $detail.=getkeyname(I('post.orderstatus'), 'OrderStatus');
        FLFParameter::ADD_OrderLog('orderlog', array(
            'orderID' => $id,
            'adminID' => getAdminID(),
            'userOp' => 3,
            'opdetail' => $detail,
            'optime' => time(),
            'opIP' => get_client_ip(0),
        ));

        switch ($data['orderstatus']) {
            case $ostat['等待确认']:
                $psd = I('post.');
                $psd['aid'] = getAdminID();
                if (M('goodsorder')->save($psd))
                    $this->success('订单确认成功', U('index', array('gid' => $gid, 'id' => $id)));
                else
                    $this->error('订单确认失败请重试');
                break;
            case $ostat['客户经理确认']:
                if (M('goodsorder')->save(I('post.')))
                    $this->success('订单确认成功', U('index', array('gid' => $gid, 'id' => $id)));
                else
                    $this->error('修改失败请重试');
                break;
            case $ostat['客服确认']:
                if (M('goodsorder')->save(I('post.'))) {
                    $userid = $data['uid'];
                    $user = D('UserView')->where(array('id' => $userid))->find();
                    $add = array();
                    $arr['uid'] = $userid;  //所属用户
                    $arr['time'] = time();         //创建时间
                    $arr['number'] = $data['number']; //工单号==订单号
                    $arr['title'] = C('AutoWo.WoTitle'); //订单标题
                    $arr['email'] = $user['username']; //联系邮箱
                    $arr['cid'] = C('AutoWo.TecGroupID'); //所属类别
                    $arr['phone'] = $user['telephone']; //手机号码
                    $arr['remark'] = '';
                    $arr['orderid'] = $id;
                    $arr['wotype'] = C('WoType.syswo');
                    if ($id = M('Wo')->add($arr)) {
                        //生成工单记录
                        create_record(M('Wo_record'), '系统自动生成工单', array('wid' => $id, 'type' => 1));
                    }
                    $this->success('订单核查成功', U('index', array('gid' => $gid, 'id' => $id)));
                } else
                    $this->error('本次操作没有改变订单状态');
                break;
            case $ostat['正在施工']:
                if (M('goodsorder')->save(I('post.'))) {
                    $this->success('订单状态修改成功', U('index', array('gid' => $gid, 'id' => $id)));
                } else
                    $this->error('本次操作没有改变订单状态');
                break;

            case $ostat['施工完毕']:
                if (M('goodsorder')->save(I('post.'))) {
                    $this->success('订单状态修改成功', U('index', array('gid' => $gid, 'id' => $id)));
                } else
                    $this->error('本次操作没有改变订单状态');
                break;

            case $ostat['正在回访']:
                if (M('goodsorder')->save(I('post.'))) {
                    $this->success('订单状态修改成功', U('index', array('gid' => $gid, 'id' => $id)));
                } else
                    $this->error('本次操作没有改变订单状态');
                break;
            case $ostat['正在试用']:
                if (M('goodsorder')->save(I('post.'))) {
                    $this->success('订单状态修改成功', U('index', array('gid' => $gid, 'id' => $id)));
                } else
                    $this->error('本次操作没有改变订单状态');
                break;
            case $ostat['试用跟进']:
                if (M('goodsorder')->save(I('post.'))) {
                    $this->success('订单状态修改成功', U('index', array('gid' => $gid, 'id' => $id)));
                } else
                    $this->error('本次操作没有改变订单状态');
                break;
            case $ostat['客户付款']:
                $pst = I('post.');
                if (M('goodsorder')->save($pst)) {
                    $this->success('订单状态修改成功', U('index', array('gid' => $gid, 'id' => $id)));
                } else
                    $this->error('本次操作没有改变订单状态');
                break;
        }
    }

    //订单的删除操作
    public function del() {
        //if(!$this->ispost()) $this->redirect('Order/index');
        $id = intval($_REQUEST['id']);
        $db = M('Goodsorder');

        //先判断这条订单是否是未支付的
        if ($db->where(array('id' => $id))->getField('status') == 0) {

            //将订单和订单的参数删除
            if ($db->where(array('id' => $id))->delete()) {

                M('parameter')->where(array('oid' => $id))->delete();

                $this->success('删除成功');
            } else {

                $this->error('删除失败');
            }
        } else {
            $this->redirect('Order/index');
            // echo $db->getLastSql();
        }
    }

    //用户订单管理
    public function userdindan(){
        
         $t = I('get.');  
        if (!empty($t['xname']))
            $where['username'] = array('like', '%' . trim($t['xname']) . '%');

        if (empty($t['max-date']))
            $where['regtime'] = array(array('EGT', strtotime($t['min-date'])), array('ELT', time()), 'AND');
        else
            $where['regtime'] = array(array('EGT', strtotime($t['min-date'])), array('ELT', strtotime($t['max-date'])), 'AND');
       

        $db = D('DindanView');
        $page = X($db, $where, '', getUrlParameter($t));
        $data = $db->where($where)->scope('orderby')->limit($page['0']->limit)->select();
        $this->order = $data;
        $this->fpage = $page['1'];
        $this->display();
    }
    
     public function deluserdindan(){        
  
        $db=M('userdindan');
        if($db->where(array('id' => I('get.id'), 'salt' => I('get.salt'),'status'=>0))->delete())
        {
              $this->success('删除成功', U('userdindan'));
        } else {       
            $this->error('删除失败');
        }   
    }
    
    public function modiuserdindan() {
        if (!$this->ispost()) {
            $data['status'] = 0;
            $data['message'] = '非法的请求';
            $this->ajaxReturn($data, 'JSON');
            die(0);
        }

        $dd = D('Userdindan');    
        $pst = I('post.');
        $where = array('id' => I('post.cid', 'intval'), 'salt' => $pst['salt']);
        $data=array();        
        $data['moditime'] = getCurenTime();
        $data['status'] = $pst['status'];
    
        if ($dd->where($where)->save($data)) {
            $data['status'] = 0;
            $data['message'] = '修改成功';
        } else {
            $data['status'] = 1;
            $data['message'] = '修改失败';
        }
        $this->ajaxReturn($data, 'JSON');
    }

}
