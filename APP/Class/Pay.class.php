<?php
/*
  @der 该类是购买产品公共继承的类，此类包含了购买必须用到的操作
*/
Class pay{
	protected $error=0; //错误号
	protected $errorMsg=''; //错误信息
//	private $db=M('Goodsorder');  //订单表的数据库连接
//	private $where=array('uid'=>$_SESSION['id'],'id'=>$_SESSION['goodsInfo']['id']);//订单表的where条件
	protected $goodsType=0;//产品是否是待处理，1代表待处理
    protected $remark='购买云产品';


//检测订单表内容是否属实
protected function test($db,$where){		
       if($order=$db->where($where)->find()){
            return $order;
       }else{
       		$this->errorNumber(1);
            return false;
       }

}



//财务记录生成及扣费操作
protected function bill($order){
	//扣除用户账户上的金钱
	if($order['price'] == 0 || M('money')->where(array('uid'=>$order['uid']))->setDec('money',$order['price'])){
        //生成财务记录
	  	$bill=array(
	  		'time'=>time(),//生成时间
	  		'price'=>$order['price'],
	  		'type'=>0,
	  		'number'=>$order['number'],
	  		'uid'=>$order['uid'],
	  		'remark'=>$this->remark
	  		);
    
        //添加财务记录
	  	if(!$bool=M('Bill')->add($bill)){

              $this->errorNumber(3);
              return false;
	  	}

	  	//删除订单参数表的内容
	  	M('Parameter')->where(array('oid'=>$order['id']))->delete();
       
        return true;
		  
    }

}

//根据错误号返回错误信息到订单表中
protected function errorNumber($number){
    $this->error=$number;  

	switch($number){
		case 1:
		    $this->errorMsg='订单号有误';
		    break;
		case 2:
		    $this->errorMsg='添加实例时出错';
		    break;		
		case 3:
		    $this->errorMsg='生成财务记录失败';
		    break;
		case 4:
		    $this->errorMsg='修改订单表信息出错';
		    break;
		case 5:
		    $this->errorMsg='您的账户余额不足';
		    break;
		case 6:
		    $this->errorMsg='修改产品信息时出错';
		    break;
		case 7:
		    $this->errorMsg='升级出错';
		    break;
		default:
		    $this->errorMsg='未知错误';
		    break;

	} 
	//调用修改订单信息的方法
	$this->set_order();
}

//修改订单表状态的方法
protected function set_order(){
	$where=array('uid'=>  getUserID(),'id'=>$_SESSION['goodsInfo']['oid']);
	$save=array('status'=>3,'remark'=>$this->errorMsg);
    M('Goodsorder')->where($where)->setField($save);
   
}

//判断用户账户上的余额是否充足
protected function is_money($price){

	//取出用户的账户余额
	$money=M('Money')->where(array('uid'=>  getUserID()))->getField('money');
	
	if($money < $price){
		$this->errorNumber(5);
		return false;
	}else{
		return true;
	}

}



//获取私有属性的魔术方法
public function __get($key){
      if(array_key_exists($key,get_class_vars(get_class($this)))){
      	 return $this->$key;
      }
}

//产品的验证操作
protected function set_pay(){
  $db=M('Goodsorder');     
  $where=array('uid'=>  getUserID(),'id'=>$_SESSION['goodsInfo']['oid']);
   if(!$order=$this->test($db,$where)){   	   
   	   return 'error';
   }

   //判断该订单是否生成了财务记录
   if(M('Bill')->where(array('uid'=>$order['uid'],'number'=>$order['number']))->getField('id')){
   	    redirect(__APP__);
   }

   //判断用户金钱是否足够
   if(!$this->is_money($order['price'])){   	  
   	   return 'error';
   }

   return $order;   
}

/*
  @der 处理修改订单的方法
*/
protected function set_order_status(){

    $db=M('Goodsorder');     
    $where=array('uid'=>  getUserID(),'id'=>$_SESSION['goodsInfo']['oid']);  
    $order=$this->set_pay(); 
    if($order=='error') return false;
  
    //修改订单状态
    if($db->where($where)->setField(array('status'=>1,'paytime'=>time(),'remark'=>'交易成功'))){
    	
      return $order;              
    }else{

      $this->errorNumber(4);
      return false;
    }
}

}