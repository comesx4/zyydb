<?php
/*
  @der 用户服务控制器
*/
Class UserServiceAction extends CommonAction{

	/*
     @der 服务列表
	*/
    public function index(){
    	//查找出该用户的所有服务
    	$service=D('User_serviceView')->where(array('uid'=>$_SESSION['id']))->select();
    	   
    	$this->service=$service;
    	$this->display();    	
    }


    /*
      @der 服务详情
    */
    public function detailed(){
    	if(empty($_GET['id'])) redirect(__APP__);

        $where=array('id'=>$this->_get('id','intval'),'uid'=>$_SESSION['id']);
        $service=D('User_serviceView')->where($where)->find();
        if(empty($service)) redirect(__APP__);
        
        $service['per']=mymd5("{$service['wid']}||{$service['id']}");
        $this->service=$service;
    	$this->display();
    }

    /*
     @der 续费页面
    */
    public function renew(){
        if(empty($_GET['id'])) redirect(__APP__);
        $sid=$this->_get('id','intval');
        //查找出该服务的所属服务ID
        $id=M('User_service')->where(array('id'=>$sid))->getField('service_id');
        if(empty($id)) redirect(__APP__);
        
         //查找出该环境的详细信息
        $where=array('sid'=>$id,'time'=>array('neq',0));       
        $service=D('ServiceView')->where(array('id'=>$id))->find();
         
        //查找出该服务的规格
        $spec=M('Service_spec')->field('id,spec')->where($where)->select();

        //查找该服务的介绍
        $this->introduce=htmlspecialchars_decode( M('Service_introduce')->where($where)->getField('introduce') );
        $this->spec=$spec;
        $this->service=$service;
        $this->id=$sid;
        
        $this->display();
    }


    /*
      @der 续费订单生成操作      
    */
    public function createOrder(){        
        if(!$this->ispost()) redirect(__APP__);
        $db=D('GoodsOrder');
        list($data,$add) = $db->create_service();
        $data['orderType'] = 1;                    //订单类型（续费订单）
        $add['id'] = $this->_post('id','intval'); //要续费服务的ID

        if(!$oid = $db->createOrder($data,$add)){
            $this->error('遇到错误，请重试！');
        }
        
        //跳转至支付页面
        redirect(U('Shopping/payWay',array('order'=>$oid)));
    }


}