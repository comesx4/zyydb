<?php
/*
  @der 订单表的模型
*/
Class GoodsOrderModel extends Model{

	/*
     @der 生成服务订单数据的方法
     return array(订单表数据，订单参数表数据);
	*/
    public function create_service(){
    	$service_id = I('service_id'); //服务表的ID
        $spec_id = I('spec_id');
        
        //取出该规格的信息
        $spec = M('Service_spec')->field('id,spec,price,time')->where(array('id'=>$spec_id))->find();

        $time = $spec['time']==0?'单次':$spec['time'].'个月';
        
        //查找出该服务的信息
        $service = M('Service')->field('id,title')->where(array('id'=>$service_id))->find();

        //添加到订单表的内容
		$data=array(
			'createtime'=>time(),
			'uid'=>$_SESSION['id'],
			'number'=>getNumber(),
			'gid'=>$service['id'],
			'quantity'=>1,
			'price'=>$spec['price'],
			'time'=>$time,
			'info'=>$spec['spec'],
			'type'=>1
		); 
    	
    	//参数表的内容
    	$add=array(
            'service_id'=>$service['id'], //服务ID
            'spec_id'=>$spec['id'], //规格ID
            'uid'=>$_SESSION['id'], //用户ID  
            'endTime'=>$spec['time'],  
            'remark'=>'规格：'.$spec['spec']           
    		);

    	return array($data,$add);
    }


    /*
     @der 生成产品订单数据的方法
     @param int $type 类型：
     					0、购买
     					1、续费
     					2、升级
     					
     @return array(订单表数据，订单参数表数据);
	*/
    public function create_goods($type = 0){    	
    	$post=arr_filter($_POST);
    	
		if (post_isnull('cpu','daikuans','goodsId','memory','num','region','time')) {
			redirect(__APP__);
		}

    	//将信息存进session中
		$_SESSION['goodsInfo']=$post;
        $pr = array();
        //核对提交过来的价钱
		switch ($type) {
			case 0:
				$pr=countPrice($post,true);
				break;
			case 1:
				$pr=$this->get_new_price(1,true);
				break;

			case 2:
				$pr['0']=$this->get_new_price(0);
				break;
		}
		
		// if(intval($pr['0']) != intval($post['countPrice'])){
		// 	return array('-1','哎呀出错啦');
		// }
		$post['countPrice'] = intval($pr['0']);

		if($post['xitong'] == 0) {
			return array('-1','请选择操作系统');
		}
		//根据产品ID查找出产品的名称
		$_POST['goodsId']=M('Goods')->where(array('id'=>intval($post['goodsId'])))->getField('goods');
       
       //根据地域ID查找出地域名
		$_POST['region']=M('City')->where(array('id'=>intval($post['region'])))->getField('city');

       //根据系统ID查找出系统名称
		$os=M('Mirroring')->field('oid,type,name')->where(array('id'=>$post['xitong']))->find();

		if($os['type']==1){
            $_POST['xitong']=$os['name'];
		}else{			
			$_POST['xitong']=M('Os')->where(array('id'=>$os['oid']))->getField('name');
		}

        if($_POST['memory']==0) {
        	$_POST['memory']='512M';
        } else{
        	$_POST['memory']=$_POST['memory'].'G';
        }

		//单价
		$_POST['unit']=$pr['1'];

        $_SESSION['goodsInfo']['number']=getNumber();

        //如果是升级查找出原来的属性
        if ($type == 2) {
        	$prevAttr = M()->query("SELECT l.status,s.memory,s.cpu,s.band 
					        		FROM kz_living l LEFT JOIN kz_yunji_server s ON l.tid = s.id WHERE l.id = {$_SESSION['living_id']}"
					        		)['0'];
        
        	if ($prevAttr['status'] != 4 && $post['cpu'] != $prevAttr['cpu'] || $prevAttr['status'] != 4 && $post['memory'] != $prevAttr['memory']) {
        		return array(-1,'请先将服务器关机再进行操作!');
        	}

        	if($prevAttr['memory'] == 0) {
        		$prevAttr['memory'] == '512M';
        	}
        	$prevAttr['cpu'] 	.= '  ->  ';
        	$prevAttr['memory'] .= '  ->  ';
        	$prevAttr['band']   .= '  ->  ';
        } else {
        	$prevAttr = '';
        }
      
       //添加到订单表的内容
		$data=array(
			'createtime'=>time(),
			'uid'=>$_SESSION['id'],
			'number'=>$_SESSION['goodsInfo']['number'],
			'gid'=>$_SESSION['goodsInfo']['goodsId'],
			'quantity'=>$post['num'],
			'price'=>$post['countPrice'],
			'time'=>$post['time'].'个月',
			'info'=>"CPU：{$prevAttr['cpu']} {$_POST['cpu']}核<br/>内存：{$prevAttr['memory']} {$_POST['memory']}<br/>带宽：{$prevAttr['band']} {$_POST['daikuans']}Mbps(固定带宽) <br/>操作系统：{$_POST['xitong']}<br/>地域：{$_POST['region']}"
		);    
        
        if (!empty($post['disk'])) {
        	$data['info'] .= "<br/>新增数据盘：".count($post['disk'])."块";
        	$_SESSION['goodsInfo']['disk'] = implode(',', $post['disk']);
        }
        
        //添加到订单信息表的内容
		$parameter['band']=$_SESSION['goodsInfo']['daikuans'];  //带宽
		$parameter['disk']=$_SESSION['goodsInfo']['disk'];    //硬盘
		$parameter['city']=$_SESSION['goodsInfo']['region'];  //城市
		$parameter['gid']=$_SESSION['goodsInfo']['goodsId'];  //产品ID
		$parameter['price']=$_SESSION['goodsInfo']['countPrice'];//价钱
		$parameter['cpu']=$_SESSION['goodsInfo']['cpu'];//CPU
		$parameter['memory']=$_SESSION['goodsInfo']['memory'];//内存
		$parameter['uid']=$_SESSION['id'];   //用户ID
		$parameter['osid']=$_SESSION['goodsInfo']['xitong']; //镜像ID
		$parameter['end']=$post['time'];     //购买时长
		$parameter['id']  = $_SESSION['living_id'];

		return array($data,$parameter);
    }



    /*
    @der生成订单和订单参数表的方法
    @parm array $data 添加到订单表的内容
    @parm array parameter 添加到订单参数表的内容
	*/
	public function createOrder($data,$parameter){
		
	     //将内容添加到订单表
	    if(!$oid=M('Goodsorder')->add($data)){
	        return false;
	    }
	    $arr=array();
	        
	    //组合订单参数表的内容
	    foreach($parameter as $key=>$v){ 
	        $arr[]=array(
	           'key'=>$key,
	           'value'=>$v,
	           'oid'=>$oid
	         );
	    }	  
	    //将数据插入到订单参数表中
	    if(!M('Parameter')->addAll($arr)){	    
	      return false;
	    }	   
	    return $oid;
	}

	/**
		@der 计算升级的价钱
		@param int $type 类型
						0、升级
						1、续费
		@return int 价钱
	*/
	public function get_new_price($type = 0,$ishop = false){
	   /*查找出该产品之前的性能*/
       $where = array('id' => $_SESSION['living_id']);
       $prev  = D('LivingView')->where($where)->find();
       $where = array('id'=>$prev['tid']);
       $pat   = M($prev['table_name'])->field('cpu,band AS daikuans,memory,city AS region')->where($where)->find();
       $pat['time']    = ($prev['end'] - $_SERVER['REQUEST_TIME']) / 2592000;
       $pat['goodsId'] = $prev['gid'];
       $pat['num']     = 1;
      
       if ($type == 1) {
        	$pat['time'] = I('post.time',1);
        	
        	//查找出改产品的所有数据盘
        	$where  = array('cloud_server_id' => $prev['tid'] , 'target' => array('NEQ' , 'vda'));
        	$disk   = M('Cloud_disk')->field('size')->where($where)->select();
        	//如果有数据盘
        	if (!empty($disk)) {
        		$pat['disk'] = '';
        		foreach ($disk as $key => $v) {
        			$pat['disk'][] = ($v['size']/1024);
        		}
        	}        	
       		return countPrice($pat,$ishop);
       }
      
       $_POST['time']  = $pat['time'];
       /*原来的价钱*/
       $price = countPrice($pat,true)['0'];
      
       /*本次选择的价钱*/
       $newPrice = countPrice($_POST,true)['0'] - $price;
  
       ($newPrice < 0) && $newPrice = 0;

       return $newPrice;
	}
}