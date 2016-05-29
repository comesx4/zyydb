<?php

//导入购买类
import('Class.Pay', APP_PATH);
import('Class.Tool', APP_PATH);

Class Goods extends Pay {

//用户产品表和实例表数据的添加
    private function addLiving($order) {
        //根据该订单的所属产品去查询该产品是否是需要进行审核的产品
        $goods = M('Goods')->field('cid,goodstype,table_name')->where(array('id' => $order['gid']))->find();

        //查找出该产品的表名称					
        //$cat=M('Goodscat')->field('tablename,id')->where(array('id'=>$goods['cid']))->find();
        //$table=str_replace('kz_','',$cat['tablename']);
        $table = str_replace('kz_', '', $goods['table_name']);
        //去订单参数表查找出数据    
        $parameter = M('Parameter')->where(array('oid' => $order['id']))->select();

        //组合内容
        $arr = array();
        $arr['cid'] = $goods['cid'];

        foreach ($parameter as $v) {
            $arr[$v['key']] = $v['value'];
        }

        $arr['uid'] = getUserID();

        $tb = M($table);
        $tid = array();
        //$serverName = array();
        //根据产品的数量添加对应的记录(用户产品表)
        for ($i = 0; $i < $order['quantity']; $i++) {
            // $serverName[] = Tool::randServerName();
            // $arr['name']  = $serverName[$i];
            $tid[] = $tb->add($arr);
        }

        $arr['start'] = time();
        $arr['end'] = time() + ($arr['end'] * 3600 * 24 * 30);

        /* 如果不用审核 */
        if ($goods['goodstype'] != 1) {
            $arr['status'] = 3; //创建中   
            $this->goodsType = 0;
        } else {
            $arr['status'] = 0; //审核中
            $this->goodsType = 1;
        }

        //根据产品的数量添加对应的记录(实例表) 
        $db = M('Living');
        for ($i = 0; $i < $order['quantity']; $i++) {
            $arr['tid'] = $tid[$i];
            $isTrue = $db->add($arr);
        }

        //如果有数据盘
        if (!empty($arr['disk'])) {
            $arr['disk'] = explode(',', $arr['disk']);
        }

        /* 请求接口 */
        switch ($order['gid']) {
            case '1' ://基础云
                import('Class.Server', APP_PATH);
                $where = array('city_id' => $arr['city'], 'status' => 1);
                $region_id = M('Mu_region')->field('id')->where($where)->select();
                $arr['region_id'] = peatarr($region_id, 'id');
                $arr['image_id'] = $arr['osid'];
                $arr['size'] = $arr['disk'];
                $arr['wan_upload_bandwidth'] = $arr['band'];

                /* 无需审核的产品 */
                if ($goods['goodstype'] != 1) {

                    $length = count($tid);
                    /* 购买多台服务器时重复请求 */
                    for ($i = 0; $i < $length; $i++) {
                        $arr['server_id'] = $tid[$i];
                        $arr['name'] = $serverName[$i];

                        $result = json_decode(Server::createServer($arr), true);

                        if ($result['code'] == 0) {
                            $where = array('id' => $arr['server_id']);
                            //记录错误信息
                        } else {
                            $save = array(
                                'remark' => $result['error'],
                                'status' => 1, //创建失败
                            );
                            $db->where(array('tid' => $arr['server_id']))->save($save);
                        }
                    }
                }
                break;
            case '17' ://腾讯云
                break;
            case '18' ://阿里云
                break;
        }
        return $isTrue;
    }

//产品的支付操作
    public function pay_goods() {

        if (!$order = $this->set_order_status())
            return false; //修改订单状态

        $this->remark = '购买云产品';

        //添加产品和实例表数据
        if (!$this->addLiving($order)) {
            echo 11;
            die;
            return false;
        }
        //生成财务记录操作
        if (!$this->bill($order)) {
            $this->errorNumber(3);
            return false;
        }
        
        //添加订单记录
        $detail = '支付订单：';      
        $detail.='-->';
        $detail.=$order['price'];
        FLFParameter::ADD_OrderLog('orderlog', array(
            'orderID' => $order['id'],
            'userID' => $order['uid'],
            'userOp' => 2,
            'opdetail' => $detail,
            'optime' => time(),
            'opIP' => get_client_ip(0),
        ));        
        return $order;
    }

    /*
      @der 续费操作
     */

    public function renew_goods() {

        if (!$order = $this->set_order_status())
            return false;
        $this->remark = '云产品续费';

        if (!$this->saveGoods($order)) {
            return false;
        }

        //生成财务记录操作
        if (!$this->bill($order)) {
            return false;
        }

        return $order;
    }

    /*
      @der 给用户产品续费的操作
     */

    private function saveGoods($order) {

        //去订单参数表查找出数据
        $parameter = M('Parameter')->where(array('oid' => $order['id']))->select();

        //组合内容
        $arr = array();
        foreach ($parameter as $v) {
            $arr[$v['key']] = $v['value'];
        }

        //查找出该产品的到期时间
        $living = M('Living')->field('end,tid,cid')->where(array('id' => $arr['id']))->find();
        $endTime = $living['end'];

        // if($endTime<time()){
        //   $endTime=time()+($arr['end']*2592000);
        // }else{
        //   $endTime=$endTime+($arr['end']*2592000);
        // }

        $save = array(
            'end' => strtotime("+{$arr['end']} month", $endTime),
            'id' => $arr['id']
        );

        //修改实例表的到期时间
        if (!M('Living')->save($save)) {
            $this->errorNumber(2);
            return false;
        }

        return true;
    }

    /**
      @der 用户产品升级操作
     */
    public function upGoods() {
        if (!$order = $this->set_order_status())
            return false;
        $this->remark = '云产品升级';

        if (!$this->up_goods($order)) {
            return false;
        }

        //生成财务记录操作
        if (!$this->bill($order)) {
            return false;
        }

        return $order;
    }

    /*
      @der 处理云产品升级
     */

    public function up_goods($order) {
        //去订单参数表查找出数据
        $parameter = M('Parameter')->where(array('oid' => $order['id']))->select();

        //组合内容
        $arr = array();
        foreach ($parameter as $v) {
            $arr[$v['key']] = $v['value'];
        }

        $cloud_server_id = M('Living')->where(array('id' => $arr['id']))->getField('tid');

        import('Class.Server', APP_PATH);
        $data = array(
            'uid' => $order['uid'],
            'cloud_server_id' => $cloud_server_id,
            'cpu' => $arr['cpu'],
            'memory' => $arr['memory'],
            'band' => $arr['band']
        );

        if (!empty($arr['disk'])) {
            $data['disk'] = explode(',', $arr['disk']);
        }

        $result = json_decode(Server::upServer($data), true);

        if ($result['code'] != 0) {
            $this->errorNumber(7);
            return false;
        }

        // //查找出该产品的到期时间
        // $living=M('Living')->field('tid,gid')->where(array('id'=>$arr['id']))->find();
        // //获取表名称
        // $table=M('Goods')->where(array('id'=>$living['gid']))->getField('table_name');
        // $arr['id'] = $living['tid'];
        // //修改产品信息
        // M($table)->save($arr);


        return true;
    }

}
