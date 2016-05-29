<?php

// 产品控制器
Class ProductAction extends Action {

    public function index() {
        $id = I('get.id', 1, 'intval');
        /* 套餐云 */
        if ($id && $id != 1) {
            $pg = M(C("pack_goods{$id}"))->order('sort DESC')->select();
            $this->package = $pg;            
            $ginfo = C('ProductInfo' . $id);
            
            //生成表单标题
            $tabletitle = '<tr>';
            foreach ($ginfo as $key => $v) {
                if (checkShow($v))
                    $tabletitle .='<td>' . $v['des'] . '</td>';
            }
            $tabletitle.='<td>价格（每月）</td>
                                      <td>时间</td>
                                      <td>操作</td></tr>';
            $this->tbtitle = $tabletitle;
            
            //生成产品列表
            $tcon="";
            $monthlist=FLFParameter::getStatusList( 'time','MonthSelect');
            foreach ($pg as $pv) {
                $tablecon = '<tr>';
                foreach ($ginfo as $key => $v) {
                    if (checkShow($v))
                    {
                       $val = getconfigval($v, $pv[$key]);
                        $tablecon .='<td>' . $val . '</td>';
                    }
                }
                $tablecon .='<td>' . $pv['price'] . '</td>';
                $tablecon .='<td>' . $monthlist . '</td>';
                $tablecon .='<td><a gid=' . $id . ' pid=' . $pv['id'] . ' class="button buy" >购买</a></td>';
                $tablecon.='</tr>';
                $tcon .=$tablecon;
            }           
            $this->tbcon=$tcon;
        }
        //先取出所有的产品分类
        $goodsCat = D('Goodscat')->relation(true)->field('id,cat')->order('sort ASC')->select();
        $this->goodsCat = $goodsCat;

        $db = M('Goodsattr');
   
        
        //取出标题部分
        $this->title = $db->where(array('gid' => $id, 'istitle' => array('eq', 1)))->getField('content');

        //根据产品的ID去求出他下面的属性
        $where = array('gid' => $id, 'istitle' => array('neq', 1));
        $this->attr = $db->field('content,attr')->where($where)->order('sort ASC')->select();

        //属性列表
        $list = peatarr($this->attr, 'attr');
        $this->list = $list;

        //产品介绍图
        $g=M('Goods')->where(array('id' => $id))->find();
        $this->infor =$g; //M('Goods')->where(array('id' => $id))->getField('introduction_img');

        $this->display();
    }

    //购买页面
    public function buy() {
     
          $db_mirroring = M('Mirroring');
          $id=I('get.id','intval');
          //首先去查找这个产品有没有订制的规则
          if(!$arr=M('Goodsprice')->field('cid')->where(array('gid'=>$id))->select()) {
          $this->redirect('Index/index');
          }

          //查找出操作系统
          $os=$db_mirroring->field('id,oid')->where(array('type'=>0))->select();

          foreach($os as $key=>$v){
          $os[$key]['name']=M('Os')->where(array('id'=>$v['oid']))->getField('name');
          }

          $this->os=$os;

          //查找出镜像的所有分类
          $cat=getCat_one('镜像',0);
          $cats=array();
          foreach($cat as $key=>$v){

          if($v['pid']==$cat['0']['pid']){
          $cats[]=$v;
          }
          }

          $this->cat=$cats;

          //如果是从镜像市场跳转过来的
          if(!empty(I('get.mid'))&&!empty(I('get.city'))){
          //查找出镜像信息
          $mirr=$db_mirroring->field('id,name')->where(array('id'=>I('get.mid')))->find();
          $mirr['cid']=I('get.city');
          $this->mirrInfo=$mirr;
          }

          //将数组组合成一位数组，并且清除重复的值
          $arr=array_unique(peatarr($arr,'cid'));

          //查找出该产品的地域
          $where=array('id'=>array('in',$arr));
          $city=M('City')->field('id,city')->where($where)->select();
          $this->city=$city;

          //查找出产品名称
          $this->goodsName=M('Goods')->where(array('id'=>$id))->getField('goods');
          //如果登陆了，则查找出自定义镜像
          if (!empty(getUserID())) {
          $where = array('uid' => getUserID() , 'type' => 2 , 'status' => 1);
          $this->customImage = $db_mirroring->field('id,name,image_code')->where($where)->order('id DESC')->select();
          }

        $this->display();
    }

    //异步获取价钱的方法
    public function getPrice() {
        if (!$this->isAjax())
            $this->redirect('Index/index');
        
        if (!empty($_POST['disk'])) {
            $_POST['disk'] = explode(',', I('post.disk'));
        }
        
        //调用计算价钱的方法计算价钱
        countPrice($this->_post());
    }

    //异步获取镜像信息的方法
    public function getMirr() {
        if (empty(I('get.cid')) && empty(I('get.gid')))
            redirect(__APP__);

        //缓存
        if (!$arr = S($_SERVER['REQUEST_URI'])) {

            $gid = I('get.gid');
            $cid = I('get.cid');
            $where = array('goods_id' => $gid);

            //分页类的where
            $pwhere = "gid={$gid}&cid={$cid}";

            //先去镜像和产品中间表查找出ID
            $id = M('Mirr_goods')->field('id')->where($where)->select();

            //去镜像和地域的中间表查找出镜像ID
            $where = array('sid' => array('IN', implode(',', peatarr($id, 'id'))), 'cid' => $cid);
            $mid = M('Mirr_city')->field('mid')->where($where)->select();

            $mid = peatarr($mid, 'mid');
            $where = array('id' => array('IN', implode(',', $mid)));

            //判断有没有传进镜像分类
            if (isset($_GET['mirrType']) && $_GET['mirrType'] != 0) {
                $mirrType = $_GET['mirrType'];
                $pwhere.="&mirrType={$mirrType}";

                //取出该分类下所有子分类ID
                $cid = get_son($mirrType, 'Mirroringcat');
                $cid[] = $mirrType;

                $where['cid'] = array('IN', implode(',', $cid));
            }

            //导入ajax分页类
            import('Class.APage', APP_PATH);
            $sum = D('MirrView')->where($where)->count();
            $page = new Page($sum, 4, $pwhere);

            //调用mirroring（镜像表视图模型）
            $mirr = D('MirrView')->where($where)->limit($page->limit)->select();

            $arr['content'] = $mirr;
            if ($sum > 4)
                $arr['page'] = $page->fpage();

            S($_SERVER['REQUEST_URI'], $arr, 3600 * 24);
        }

        if (empty($arr['content']))
            echo '该分类暂无镜像';

        $this->fpage = $arr['page'];
        $this->mirr = $arr['content'];

        $this->display();
    }

    /**
      @der 套餐云的订单生成
     */
    public function buyPackage() {
       $userid = checkLogin();
        IS_POST || gotoGateWay();
        post_isnull('id', 'time', 'gid') && gotoGateWay();

        $gid = I('post.gid', '', 'intval');
        $time = I('post.time', '', 'intval');
        $id = I('post.id', '', 'intval');
        $arr = array(
            'name' => '套餐名称',
            'band' => '带宽',
            'cpu' => 'CPU',
            'memory' => '内存',
            'disk' => '硬盘',
            'cabinet_size' => '机柜大小'
        );
        
        $where = array('id' => $id);
        $data = M(C("pack_goods{$gid}"))->field('id,sort', true)->where($where)->find();//读取详细产品的信息
        
        //生成订单信息
        $add = array(
            'number' => getNumber(),//生成订单号
            'gid' => $gid,//所属产品
            'uid' => $userid, // $_SESSION['id'],
            'price' => $data['price'] * $time,
            'info' => '',
            'createtime' => $_SERVER['REQUEST_TIME'],
            'time' => $time . '个月',
            'pid' => $id,//商品
        );
        
        unset($data['price']);

        $parameter = array(
            'gid' => $gid,
            'uid' => $userid, // $_SESSION['uid'],
            'end' => $time     //购买时长
        );

//        foreach ($data as $key => $v) {
//            if ($key != 'type') {
//                $add['info'] .= $arr[$key] . ":{$v}<br/>";
//            }            
//            $parameter[$key] = $v;
//        }
//        $add['info'] = rtrim($add['info'], '<br/>');
        
        $x = pp2str($gid, $data, $parameter, ':', '<br/>');//将产品信息转为字符串并对 完善 parameter 参数
        $add['info'] = rtrim($x[0], '<br/>');   
        
        //创建新订单并在  parameter 表中创建订单相关产品参数
        if ($oid = D('GoodsOrder')->createOrder($add, $x[1])) {
            
            //记录一个日志
            FLFParameter::ADD_OrderLog('orderlog', array(
                'orderID' => $oid,
                'userID' => $userid,
                'userOp' => 1,
                'opdetail' => '创建新订单',
                'optime' => time(),
                'opIP' => get_client_ip(0),
            ));
            redirect(U('/Shopping/payWay', array('order' => $oid)));
        }
//        $this->display();
    } 
}
