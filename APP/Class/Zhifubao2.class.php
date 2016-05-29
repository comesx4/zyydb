<?php
Class Zhifubao{
      public $type=1; //类型（1购买产品，0为充值）
      public $urlType=1; //1为用户返回的页面，0为支付宝异步请求
      public $notify_url='http://127.0.01/aliyun/index.php/Success/index';  //服务器异步通知页面路径
      public $return_url='http://127.0.01/aliyun/index.php/Success/success';  //页面跳转同步通知页面路径
	        

      // //构造方法
      // public function __construct($type,$urlType){
      // 	 $this->type=$type;
      // 	 $this->urlType=$urlType;

      // }
      
      public function __set($key,$vlaue){
      	  if(array_key_exists($key,get_class_vars(get_class($this)))){
      	  	   $this->$key=$value;
      	  }

      }

      //处理提交到支付宝时的操作
      public function submitcent(){
      	   //导入提交表单类
		import('Class.alipay_submit',APP_PATH);
		
			//合作身份者id，以2088开头的16位纯数字
			$alipay_config['partner']= C('partner');

			//收款支付宝账号
			$alipay_config['seller_email']	= C('seller_email');

			//安全检验码，以数字和字母组成的32位字符
			$alipay_config['key']= C('key');


			//签名方式 不需修改
			$alipay_config['sign_type']=C('sign_type');

			//字符编码格式 目前支持 gbk 或 utf-8
			$alipay_config['input_charset']=C('input_charset');

			//ca证书路径地址，用于curl中ssl校验
			//请保证cacert.pem文件在当前文件夹目录中
			$alipay_config['cacert']    =C('cacert');

			//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
			$alipay_config['transport']    = C('transport');

			$_SESSION['config']=$alipay_config;

			  //支付类型
	        $payment_type = "1";
	        //必填，不能修改
	        //服务器异步通知页面路径
	        $notify_url = $this->notify_rul;
	        //"http://商户网关地址/create_direct_pay_by_user-PHP-UTF-8/notify_url.php";
	        //需http://格式的完整路径，不能加?id=123这类自定义参数

	        //页面跳转同步通知页面路径
	        $return_url = $this->return_url;
	        //"http://商户网关地址/create_direct_pay_by_user-PHP-UTF-8/return_url.php";
	        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

	        //商户订单号
	        $out_trade_no =$this->type?$_SESSION['goodsInfo']['number']:$_SESSION['goodsInfo']['number2'];
	        //商户网站订单系统中唯一订单号，必填

	        //订单名称
	        $subject =$this->type?'buy':'recharge';
	        //必填

	        //付款金额
	     //   $total_fee = $_SESSION['goodsInfo']['price'];
	        $total_fee=$this->type?'0.01':$_POST['price'];

	        //必填

	        //订单描述
	      
	        $body = $_POST['WIDbody'];

	        if($_POST['WIDdefaultbank']!=null){
	        //默认支付方式
	        $paymethod = "bankPay";
	        //必填
	        //默认网银
	        $defaultbank = $_POST['WIDdefaultbank'];
	        //必填，银行简码请参考接口技术文档
		    }

	        //商品展示地址
	        $show_url = $_POST['WIDshow_url'];
	        //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

	        //防钓鱼时间戳
	        $anti_phishing_key = "";
	        //若要使用请调用类文件submit中的query_timestamp函数

	        //客户端的IP地址
	        $exter_invoke_ip = '192.168.1.98';
	        //非局域网的外网IP地址，如：221.0.0.1

	        if($_POST['WIDdefaultbank']==null){
			//构造要请求的参数数组，无需改动
			$parameter = array(
					"service" => "create_direct_pay_by_user",
					"partner" => trim($alipay_config['partner']),
					"seller_email" => trim($alipay_config['seller_email']),
					"payment_type"	=> $payment_type,
					"notify_url"	=> $notify_url,
					"return_url"	=> $return_url,
					"out_trade_no"	=> $out_trade_no,
					"subject"	=> $subject,
					"total_fee"	=> $total_fee,
					"body"	=> $body,
					//"paymethod"	=> $paymethod,
					//"defaultbank"	=> $defaultbank,
					"show_url"	=> $show_url,
					"anti_phishing_key"	=> $anti_phishing_key,
					"exter_invoke_ip"	=> $exter_invoke_ip,
					"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
			);
	        }else{
	        		$parameter = array(
					"service" => "create_direct_pay_by_user",
					"partner" => trim($alipay_config['partner']),
					"seller_email" => trim($alipay_config['seller_email']),
					"payment_type"	=> $payment_type,
					"notify_url"	=> $notify_url,
					"return_url"	=> $return_url,
					"out_trade_no"	=> $out_trade_no,
					"subject"	=> $subject,
					"total_fee"	=> $total_fee,
					"body"	=> $body,
					"paymethod"	=> $paymethod,
					"defaultbank"	=> $defaultbank,
					"show_url"	=> $show_url,
					"anti_phishing_key"	=> $anti_phishing_key,
					"exter_invoke_ip"	=> $exter_invoke_ip,
					"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
			);

	        }

	        //修改订单状态为支付中
	        M('Goodsorder')->where(array('uid'=>$_SESSION['id'],'number'=>$_POST['WIDout_trade_no']))->setField(array('status'=>3));

        
			//建立请求
			$alipaySubmit = new AlipaySubmit($alipay_config);
			$html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");
			echo $html_text;


      }


      
      //处理支付宝返回数据的事情
      public function returncent(){
       
      	//商户订单号
			$out_trade_no = $_REQUEST['out_trade_no'];
			
			//支付宝交易号

			$trade_no = $_REQUEST['trade_no'];

			//交易状态
			$trade_status = $_REQUEST['trade_status'];
           
             $db=M('Goodsorder');
            //查询订单表的where条件
			$where=array('number'=>$out_trade_no);
           
			//查找出该订单的信息
			$order=$db->where($where)->find();			

			//判断该订单有没有充值记录
			if(M('Bill')->where(array('uid'=>$order['uid'],'number'=>$_REQUEST['trade_no']))->getField('id')){
				if($this->urlType==1){
					//跳转回首页
					redirect(__APP__);
				}else{
					echo 'success';
					die;
				}
			}
               
               //添加到充值记录表的数据
              $add=array(
                	'uid'=>$order['uid'],
                	'type'=>1,
                	'time'=>time(),
                	'price'=>$_REQUEST['total_fee'],
                	'remark'=>'充值到云账户',
                	'number'=>$_REQUEST['trade_no']
                	); 
  
             //判断用户的付款金额是否和订单表的价格一致
		    	
		    	$price=$db->where($where)->getField('price');
                $money=M('Money');
		    	
		    	//如果不一致就将金钱添加到用户的账户上，取消本次产品的购买
		    	// if($price!=$_REQUEST['total_fee']){

		    	// 	 //修改订单状态为支付失败
	      // 			  if($db->where($where)->save(array('status'=>3,'remark'=>'支付失败，余额不足!'))){
                       
	      // 			  		//增加用户的金钱
	      // 			  	  if($money->where(array('uid'=>$order['uid']))->setInc('money',$_REQUEST['total_fee'])){
       //                         //生成充值记录
	      // 			  	  	   M('Bill')->add($add);

	      // 			  	  }
	      // 			  }

	      // 		 die("充值成功!,您已充值{$_REQUEST['total_fee']}");
      			  

		    	// }
                
              
             
                 
               //先将金钱充值到用户账户表中
		    	if($money->where(array('uid'=>$order['uid']))->setInc('money',$order['price'])){
                    
                 
                     //生成充值记录
		    		if(M('Bill')->add($add)){

		    			if($this->type==0){

		    				//删除暂时性的订单
		    				$db->where($where)->delete();


		    				//判断是否为异步
		    				if($this->urlType==0){
		    					echo 'success';
		    					die;
		    					
		    				}
		    				echo '充值成功';

		    				die;
		    			}
		    				

		    			//根据该订单的所属产品去查询该产品是否是需要进行审核的产品
				    	$goods=M('Goods')->field('cid,goodstype')->where(array('id'=>$order['gid']))->find();

		              
					      //修改订单状态
		      			  if($db->where($where)->setField(array('status'=>1,'paytime'=>time(),'remark'=>'交易成功'))){

		      			  	//查找出该产品所属分类的表名称					
							$cat=M('Goodscat')->field('tablename,id')->where(array('id'=>$goods['cid']))->find();
							$table=str_replace('ali_','',$cat['tablename']);

							//去订单参数表查找出数据
							$parameter=M('Parameter')->where(array('oid'=>$order['id']))->select();

							//组合内容
							$arr=array();
							$arr['cid']=$cat['id'];

							foreach($parameter as $v){
								$arr[$v['key']]=$v['value'];
							}
							$arr['uid']=$_SESSION['id'];
							
							$tb=M($table);
							$tid=array();
		                    //根据产品的数量添加对应的记录(用户产品表)
		                    for($i=0;$i<$order['quantity'];$i++){
		                       $tid[]=$tb->add($arr);
		                    } 

		                    $arr['start']=time();
		                    $arr['end']=time()+($order['time']*3600*24*30);

		                    if($goods['goodstype']==1)
		                    	$arr['status']=1;//待处理
		               
		                    //根据产品的数量添加对应的记录(实例表) 
		                    for($i=0;$i<$order['quantity'];$i++){
		                    	$arr['tid']=$tid[$i];
		                       $isTrue=M('Living')->add($arr);
		                      
		                    } 

		                    //如果实例表的数据添加成功了
		                    if($isTrue){
		                    	//扣除用户账户上的金钱
		                    	if($money->where(array('uid'=>$order['uid']))->setDec('money',$order['price'])){
                                        //生成财务记录
					      			  	$bill=array(
					      			  		'time'=>time(),//生成时间
					      			  		'price'=>$price,
					      			  		'type'=>0,
					      			  		'number'=>$_REQUEST['trade_no'],
					      			  		'uid'=>$order['uid'],
					      			  		'remark'=>'购买云产品'
					      			  		);
					                    
					                    //添加财务记录
					      			  	M('Bill')->add($bill);

					      			  	//删除订单参数表的内容
					      			  	M('Parameter')->where(array('oid'=>$order['id']))->delete();

					      			  	//判断是否为异步
		    							if($this->urlType==0){
		    								echo 'success';
		    								die;
		    							}

		                    	}
		                    }

		      			  	
		      			  }


		    		}


		    	}
      }

}
