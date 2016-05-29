<?php
/*
  @der 购买服务时,处理服务的控制器
*/
//导入购买类
import('Class.Pay',APP_PATH);

Class Service extends Pay{


/*
  @der 给用户添加服务的操作
*/
private function addService($order){

    //去订单参数表查找出数据
    $parameter=M('Parameter')->where(array('oid'=>$order['id']))->select();
   
    //组合内容
    $arr=array();
    foreach($parameter as $v){  
      $arr[$v['key']]=$v['value'];
    }
    //服务到期时间
    $arr['endTime']=$arr['endTime']==0?$arr['endTime']:time()+($arr['endTime']*2592000);
    $arr['createTime']=time();
    if(!M('User_service')->add($arr)){        
     $this->errorNumber(2);        
     return false;
    }
    
    //服务的交易次数+1
    M('Service')->where(array('id'=>$arr['service_id']))->setInc('num');
    
    return true;
}


/*
  @der 服务的支付操作
*/
public function pay_goods(){
    if(!$order=$this->set_order_status()) return false;
    $this->remark='购买云服务';

    if(!$this->addService($order)){
      return false;
    }

     //生成财务记录操作
    if(!$this->bill($order)){         
      return false;
    }           
      return $order;      
}

/*
  @der 续费操作
*/
public function renew_goods(){
    if(!$order=$this->set_order_status()) return false;
    $this->remark='云服务续费';
    
    if(!$this->saveService($order)){
      return false;
    }

     //生成财务记录操作
    if(!$this->bill($order)){         
      return false;
    }            
      return $order;
}

/*
  @der 给用户续费服务的操作
*/
private function saveService($order){

    //去订单参数表查找出数据
    $parameter=M('Parameter')->where(array('oid'=>$order['id']))->select();   
    
    //组合内容
    $arr=array();
    foreach($parameter as $v){  
      $arr[$v['key']]=$v['value'];
    }
    
    //查找出该服务的到期时间
    $endTime=M('User_service')->where(array('id'=>$arr['id']))->getField('endTime');
    if($endTime<time()){
      $endTime=time()+($arr['endTime']*2592000);
    }else{
      $endTime=$endTime+($arr['endTime']*2592000);
    }

    $save=array(
        'endTime'=>$endTime,
        'id'=>$arr['id'],
        'remark'=>$arr['ramrk'],
        'spec_id'=>$arr['spec_id']
      );

      if(!M('User_service')->save($save)){          
           $this->errorNumber(2);        
           return false;
      }

      return true;
}




}