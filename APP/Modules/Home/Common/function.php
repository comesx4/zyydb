<?php

 /*
  异步计算价钱的方法
  array $post 需要计算的那些内容

  */
   function countPrice($post,$shop=false){
    //将POST里面的值转换成变量
    extract($post); 
    $is=false;
   
   //去打折表查看该产品有没有打折
    $sale=M('Sale')->field('month,sale')->where(array('gid'=>$goodsId))->select();
    foreach($sale as $v){
       if($v['month']==$time){
           $is=$v['sale'];
           continue;
       }  
    }
   
    //根据地域和产品ID查找出价钱
    $where=array('gid'=>$goodsId,'cid'=>$region);
    $mon=D('GoodspriceView')->where($where)->order('id ASC')->select();
    
    $arr=array(); 
    $count=count($mon);
 
    //遍历出规则
    for($i=0;$i<$count;$i++){
      $arr[$mon[$i]['name']]=$mon[$i]['price'];
    }
   
    //计算带宽每MPS的价钱
    switch($daikuans){
      case $daikuans>150:
            $daikuanPrice=$arr['band200'];
            break;
      case $daikuans>100:
           $daikuanPrice=$arr['band150'];
            break;
      case $daikuans>50:
           $daikuanPrice=$arr['band100'];
            break;
      case $daikuans>0:
           $daikuanPrice=$arr['band50'];
            break;
    }
    
    //带宽的价钱
    $daikuans=$daikuans*$daikuanPrice;

    //硬盘的价钱
    // $disk_price=$hard*$arr['disk'];
    $disk_price = 0;
    if (!empty($disk)){
        foreach ($disk as $v) {
            if ($v) {
                $disk_price += $v * $arr['disk'];
            }
        }
    }

    //cpu的价钱
    switch($cpu){
      case 1:
          $cpu=$arr['cpu1'];
          break;
      case 2:
          $cpu=$arr['cpu2'];
          break;
      case 4:
          $cpu=$arr['cpu4'];
          break;
      case 8:
          $cpu=$arr['cpu8'];
          break;
      case 16:
          $cpu=$arr['cpu16'];
          break;

    }


    //内存的价钱
    switch($memory){
      case 0:
          $memory=$arr['memory512'];
          break;
      case 1:
          $memory=$arr['memory1'];
          break;
      case 2:
          $memory=$arr['memory2'];
          break;
      case 4:
          $memory=$arr['memory4'];
          break;
      case 8:
          $memory=$arr['memory8'];
          break;
      case 16:
          $memory=$arr['memory16'];
          break;
       case 32:
          $memory=$arr['memory32'];
          break;

    }

    //判断用户选择的时间段有没有打折


    //购买时间的价钱
    $time=$time*($daikuans+$cpu+$memory+$disk_price);
   
    //总价钱
    $count=round($num*$time);
    
    if(!$shop){
      if($is){
        $data=array('status'=>1,'mess'=>'￥'.number_format($count, 2, '.', ''),'data'=>'￥'.number_format($count*$is, 2, '.', ''));
      }else{
        $data=array('status'=>0,'mess'=>'￥'.number_format($count, 2, '.', ''));
      }
      
      echo json_encode($data);

    }else{

      if($is){
        $data=number_format($count*$is, 2, '.', '');
      }else{
        $data=number_format($count, 2, '.', '');
      }

      return array($data,$time);

    }
}

/**
 * @der 替换内容
 * @param string $content 要替换的内容
 * @param int $length [长度限制]
 */
function repacle_content($content,$length = 70){
    $new = strip_tags($content);
    if (mb_strlen($new) > $length) {
        $content = mb_substr($new,0,$length,'utf-8').'...';
    }
    return $content;
}

//读取用户ID
function getUserID(){
    return I('session.' . C('SAFE.IDHAND'),0,'intval');
}

//检查用户是否登录
 function checkLogin() {
    //检查认证识别号
     $userid=getUserID();
    if ($userid==0) {
        gotoGateWay();
    }
    return $userid;
}

//跳转到登录界面
function gotoGateWay(){
    redirect(PHP_FILE .C('SAFE.AUTH_GATEWAY'),C('SAFE.AUTH_GATEWAY_TIME'),C('SAFE.AUTH_GATEWAY_MSG'));
}