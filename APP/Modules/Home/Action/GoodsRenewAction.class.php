<?php
/*
  @der 产品续费管理控制器
*/
Class GoodsRenewAction extends CommonAction{

	/*
      @der 升级页面
	*/
    public function index(){
      
    	$id=$this->_get('id','intval');   //实例表的ID
    	//用来对比的数组
    	$arr=array('cpu'=>'cpu','memory'=>'memory','disk'=>'disk','band'=>'band','city'=>'city','osid'=>'osid');
    	//查找出产品的产品信息
    	$living=M('Living')->where(array('id'=>$id))->find();

        $living || redirect(__APP__);
    	//$cat=M('Goodscat')->field('id,tablename')->where(array('id'=>$living['cid']))->find();    	
    	//$table=ucfirst(ltrim($cat['tablename'],'kz_'));
        $table = M('Goods')->where(array('id'=>$living['gid']))->getField('table_name');
    	//用户已购买的产品信息
    	$goods=M($table)->field('city,osid,band,disk,memory,cpu')->where(array('id'=>$living['tid']))->find();
    	
    	//用户产品已购买的初始值
    	$this->per=$goods;
        //用户续费产品分类所拥有的参数
        $renew=peatarr(M('Goodsrenew_performace')->field('name')->where(array('cid'=>$living['cid']))->select(),'name');
        
        //取出不同的参数
        foreach($renew as $key => $v){
        	if(array_key_exists($v,$goods)){
        		unset($goods[$v]);
        	}
        }

        $_SESSION['validate']  = $goods;  //用于提交时验证参数是否被更改
 		$_SESSION['living_id'] = $living['id'];
        //续费参数未定制的值
        $this->goods=$goods;
        $this->living=$living;
    	$this->display();
    }   

    /**
        @der 续费页面
    */
    public function renew(){
        $id=$this->_get('id','intval');   //实例表的ID
        
        //查找出产品的产品信息
        $living=M('Living')->where(array('id'=>$id))->find();
        $table = M('Goods')->where(array('id'=>$living['gid']))->getField('table_name');
        //用户已购买的产品信息
        $goods=M($table)->field('city,osid,band,disk,memory,cpu')->where(array('id'=>$living['tid']))->find();
      
        $_SESSION['validate']  = $goods;  //用于提交时验证参数是否被更改
        $_SESSION['living_id'] = $living['id'];
        //用户产品已购买的初始值
        $this->living = $living;
        $this->per=$goods;
        $this->display();
    }

    /*
        @der 异步验证价钱(升级的价钱验证)
    */
    public function getPrice(){
       IS_AJAX || redirect(__APP__);
       
       if (!empty($_POST['disk'])) {
            $_POST['disk'] = explode(',' , I('post.disk'));
       }

       $newPrice = D('GoodsOrder')->get_new_price(0) ;

       echo json_encode(array('status' => 0,'mess'=>"￥{$newPrice}"));
    }

     /*
        @der 异步验证价钱(续费的价钱验证)
    */
    public function getUpPrice(){
       IS_AJAX || redirect(__APP__);
       
       D('GoodsOrder')->get_new_price(1,false) ;

    }

    /*
       @der 生成产品续费订单的操作
    */
    public function createOrder(){
    	if(!$this->ispost() || empty($_SESSION['validate'])) redirect(__APP__);
        $orderType = I('post.orderType',2,'intval');
    	//验证用户是否篡改值    
        unset($_SESSION['validate']['disk']);	
    	foreach($_SESSION['validate'] as $key=>$v){
    		if($key=='city') $key='region';
    		if($key=='osid') $key='xitong';
    		if($key=='band') $key='daikuans';    	
    		if($_POST[$key] != $v) $this->error('参数有误!');
    	}

    	$db=D('GoodsOrder');
		//调用Model中的方法处理数据
		list($data,$parameter)=$db->create_goods($orderType,$this);			
        if($data==-1) $this->error($parameter);

        //订单类型为升级订单
        $data['orderType'] = $orderType;
        $parameter['id'] = $_SESSION['living_id'];
	    unset($parameter['gid']);
	   
	    //将内容添加到订单表和参数表中
	    if(!$oid=$db->createOrder($data,$parameter)){
	    	$this->error('遇到问题，请重试');
	    }
	    $_SESSION['living_id'] = null;
	    $_SESSION['validate']  = null;
        
	    //跳转至支付页面
        redirect(U('Shopping/payWay',array('order'=>$oid)));
    }
}