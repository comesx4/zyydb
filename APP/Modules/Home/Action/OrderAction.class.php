<?php
//订单管理控制器
Class OrderAction extends CommonAction{

	//订单列表
	public function index(){	
	   $tmp=!empty($_POST)?$_POST:$_GET;
       $where=array();
       $pwhere='';
		//如果用户单击了搜索
		if(isset($tmp['goods'])){
                
			if($tmp['status']!='w'){
				$where['status']=$tmp['status'];      
				$pwhere .="status={$where['status']}&";     

			}else{
				 $pwhere .="status=w&";     
			}
			$where['gid']=$tmp['goods'];
		    $pwhere .="goods={$where['gid']}&";    

			//产品列表
		    $this->goods=goodsList('goods',$where['gid']);

		}else{
			//产品列表
		    $this->goods=goodsList('goods');
		}
		
        $pwhere=rtrim($pwhere,'&');
		//查找出该用户的订单
		$where['uid']=$_SESSION['id'];
		
        //查找出订单的类型
        $db=D('GoodsorderView');
        //导入分页类
        import('Class.Page',APP_PATH);

        $sum=$db->where($where)->count();
     
        $page=new Page($sum,10,$pwhere);
		$order=$db->where($where)->limit($page->limit)->order('id DESC')->select();
		$this->fpage=$page->fpage();
		$this->order=$order;
		$this->display();
	}

	//订单详细页面
	public function minute(){
		$where=array('uid'=>$_SESSION['id'],'id'=>$this->_get('id','intval'));
		$order=D('GoodsorderView')->where($where)->find();
		$this->order=$order;
    
		$this->display();

	}

	//账单明细列表
	public function bill(){
        
        $db=M('Bill');
        $where=array('uid'=>$_SESSION['id']);
        if(!empty($_POST['number'])){
            $where["number"] = trim(I("post.number"));   
        }
        trace($where);
		 //导入分页类
        import('Class.Page',APP_PATH);

        $sum=$db->where($where)->count();

        $page=new Page($sum,10);

		$this->bill=$db->where($where)->order('id DESC')->limit($page->limit)->select();
		$this->fpage=$page->fpage();
		$this->display();
	}

	

	//订单的废除操作
	public function orderDel(){
		if(!$this->ispost()) redirect(__APP__);
        $db=M('Goodsorder');
		$id=$this->_post('id','intval');

		//判断该订单是否是未支付的订单
		if($db->where(array('id'=>$id))->getField('status')!=0)
			 redirect(__APP__);

	    //删除订单表
		if($db->where(array('id'=>$id))->delete()){

			//删除订单表的信息
			M('parameter')->where(array('oid'=>$id))->delete();

			echo 1;

		}else{
			echo 0;
		}
	}
}