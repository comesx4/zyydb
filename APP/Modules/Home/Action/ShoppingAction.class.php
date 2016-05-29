<?php

//购物车和生成订单控制器
Class ShoppingAction extends CommonAction {

    //订单列表
    public function index() {

        if (!$this->ispost())
            redirect(__APP__);
        $_POST = $this->_post();

        //判断是否是从镜像市场提交过来的
        if (isset($_POST['type'])) {
            //获取套餐数组
            $arr = include './Data/groom.php';
            $arr = $arr[$_POST['type']];

            $price = array(
                'cpu' => $arr['cpu'],
                'memory' => $arr['memory'],
                'daikuans' => $arr['daikuans'],
                'disk' => array('0' => $arr['hard']),
                'region' => I('post.city'),
                'time' => I('post.time'),
                'goodsId' => I('post.goods'),
                'num' => 1
            );
            $_POST = array_merge($_POST, $price);
        }

        $db = D('GoodsOrder');
        //调用Model中的方法处理数据
        list($data, $parameter) = $db->create_goods();

        if ($data == -1)
            $this->error($parameter);
        //将内容添加到订单表和参数表中
        if (!$oid = $db->createOrder($data, $parameter)) {
            $this->error('遇到问题，请重试');
        }

        $this->oid = $oid;
        $this->display();
    }

    //支付方式界面
    public function payWay() {
        //如果是之前的订单提交过来
        if (empty($_GET['order']))
            redirect(__APP__);
        $userid = getUserID();
        $where = array('id' => I('get.order', 'intval'), 'status' => array('neq', 1), 'uid' => $userid);

        //去订单表查找出记录
        $order = M('Goodsorder')->field('id,gid,type,number,quantity,price,time,info,uid,orderType')->where($where)->find();

        if (!$order)
            redirect(__APP__);

        $order['oid'] = $order['id'];
        unset($order['id']);

        $_SESSION['goodsInfo'] = $order; //在SESSION中保存订单信息

        $this->number = $order['number'];
        $this->pinfo = $order;
        $this->_getName($order); //查出产品名称
        //查找出用户的金额
        $where = array('uid' => $order['uid']);
        $money = M('Money')->where($where)->getField('money');
        $this->money = $money;

        //判断用户账户上的余额是否足够
        $this->ismoney = $money >= $order['price'] ? 0 : 1;

        $this->display();
    }

    //产品的购买操作
    public function buy() {
        if (!$this->ispost())
            redirect(__APP__);
        $ginfo = I('session.goodsInfo');
        if (empty($ginfo['oid']))
            redirect(__APP__);

        //购买的情况
        if ($ginfo['orderType'] == 0) {

            //判断是服务还是产品
            if ($ginfo['type'] == 0) {
                //导入产品购买类
                import('Class.Goods', APP_PATH);

                $goods = new Goods();

                if ($order = $goods->pay_goods()) {

                    $this->pinfo = $order;
                    $this->href = U('UserGoods/index');
                    $this->display();
                } else {
                    $this->display('fail');
                }
            } else {

                //导入服务购买类
                import('Class.Service', APP_PATH);

                $goods = new Service();

                if ($order = $goods->pay_goods()) {
                    $this->pinfo = $order;
                    $this->href = U('UserService/index');
                    $this->display();
                } else {
                    $this->display('fail');
                }
            }
            //续费的情况
        } else {

            //判断是服务还是产品
            if ($ginfo['type'] == 0) {
                //导入产品类
                import('Class.Goods', APP_PATH);

                switch ($ginfo['orderType']) {
                    case 1:
                        $fun = 'renew_goods';
                        break;

                    case 2:
                        $fun = 'upGoods';
                        break;
                }

                $goods = new Goods();

                if ($order = $goods->$fun()) {

                    $this->pinfo = $order;
                    $this->href = U('UserGoods/index');
                    $this->display();
                } else {
                    $this->display('fail');
                }
            } else {

                //导入服务类
                import('Class.Service', APP_PATH);

                $goods = new Service();

                if ($order = $goods->renew_goods()) {
                    $this->pinfo = $order;
                    $this->href = U('UserService/index');
                    $this->display();
                } else {
                    $this->display('fail');
                }
            }
        }
    }

    private function _getName($order) {
        //查找出订单的产品名称
        $db = $order['type'] == 1 ? M('Service') : M('Goods');
        $getField = $order['type'] == 1 ? 'title' : 'goods';
        $this->name = $db->where(array('id' => $order['gid']))->getField($getField);
    }

    /*
      @der 服务与定制开发的订单页面
     */

    public function order() {
        if (!$this->ispost())
            redirect(__APP__);
        $db = D('GoodsOrder');
        $service_id = $this->_post('service_id', 'intval'); //服务表的ID
        $spec_id = $this->_post('spec_id', 'intval');

        //取出该规格的信息
        $spec = M('Service_spec')->field('id,spec,price,time')->where(array('id' => $spec_id))->find();

        $time = $spec['time'] == 0 ? '单次' : $spec['time'] . '个月';

        //查找出该服务的信息
        $service = M('Service')->field('id,title')->where(array('id' => $service_id))->find();

        list($data, $add) = $db->create_service();

        if (!$oid = $db->createOrder($data, $add)) {
            $this->error('遇到错误，请重试！');
        }

        $this->service = array('title' => $service['title'], 'price' => $spec['price'], 'spec' => $spec['spec'], 'oid' => $oid);

        $this->display();
    }

}
