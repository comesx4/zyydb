<?php
Class Zhifubao{
      public $notify_url='';  //服务器异步通知页面路径
      public $return_url='';  //跳转同步通知页面路径
      public $out_trade_no; //商户订单号
      public $subject='智御云充值'; //订单名称
	        
	        

    //构造方法
    public function __construct($type,$urlType){      	 
    	$this->notify_url = 'http://test.com:8088'.U('Success/index');
    	$this->return_url = 'http://test.com:8088'.U('Success/success');
    }
      
    public function __set($key,$vlaue){
      	if(array_key_exists($key,get_class_vars(get_class($this)))){
      	  	$this->$key=$value;
      	}

    }

       //跳过支付宝充值  测试使用
      public function creRechargeOutByZhifubao(){
      	 //生成充值表的数据
		 $data=array(
		 	'money'=>$_POST['price'],
		 	'uid'=>$_SESSION['id'],
		 	'type'=>$_POST['WIDdefaultbank'],		 
		 	'number'=> getNumber(),
		 	'time'  => $_SERVER['REQUEST_TIME']
		 	);

		 $this->out_trade_no=$data['number'];
		
         //如果数据生成成功
		if(M('Recharge')->add($data)){
		  	
                    //商户订单号
			$out_trade_no = $this->out_trade_no;
			
			//支付宝交易号
			$trade_no = $this->out_trade_no;

		
           
            $db=M('Recharge');
            //查询订单表的where条件
			$where=array('number'=>$out_trade_no);
           
			//查找出该充值记录的充值信息
			$order = $db->where($where)->find();			
			
			//判断该订单有没有充值记录
			if(M('Bill')->where(array('uid'=>$order['uid'],'number'=>$trade_no))->getField('id')){
				return true;		
			}

            //添加到充值记录表的数据
            $add=array(
            	'uid'=>$order['uid'],
            	'type'=>1,
            	'time'=>time(),
            	'price'=>$_POST['price'],
            	'remark'=>'充值到云账户',
            	'number'=>$this->out_trade_no
            	); 

            $money=M('Money');
                 
            //先将金钱充值到用户账户表中
	    	if($money->where(array('uid'=>$order['uid']))->setInc('money',$order['money'])){
                    
	    		//修改充值表的状态
	    		$db->where($where)->setField(array('time'=>$_SERVER['REQUEST'],'isbuy'=>1));
             
                 //生成充值记录
	    		if(M('Bill')->add($add)) {
	    			return true;
	    		}	

	    	}

	    	return false;
		}

      }
    
      //提交支付宝之前的操作
      public function creRecharge(){
      	 //生成充值表的数据
		 $data=array(
		 	'money'=>$_POST['price'],
		 	'uid'=>$_SESSION['id'],
		 	'type'=>$_POST['WIDdefaultbank'],		 
		 	'number'=> getNumber(),
		 	'time'  => $_SERVER['REQUEST_TIME']
		 	);

		 $this->out_trade_no=$data['number'];
		
         //如果数据生成成功
		if(M('Recharge')->add($data)){
		  	$this->submitcent();
		}

      }


      //处理提交到支付宝时的操作
      private function submitcent(){
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
        $notify_url = $this->notify_url;
       
        //"http://商户网关地址/create_direct_pay_by_user-PHP-UTF-8/notify_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = $this->return_url;
        //"http://商户网关地址/create_direct_pay_by_user-PHP-UTF-8/return_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //商户订单号
        $out_trade_no =$this->out_trade_no;
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject =$this->subject;
        //必填

        //付款金额
     //   $total_fee = $_SESSION['goodsInfo']['price'];
        $total_fee=$_POST['price'];

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

        if($_POST['WIDdefaultbank']=='zhifubao'){
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
        // M('Goodsorder')->where(array('uid'=>$_SESSION['id'],'number'=>$_POST['WIDout_trade_no']))->setField(array('status'=>3));
    
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
           
            $db=M('Recharge');
            //查询订单表的where条件
			$where=array('number'=>$out_trade_no);
           
			//查找出该充值记录的充值信息
			$order = $db->where($where)->find();			
			
			//判断该订单有没有充值记录
			if(M('Bill')->where(array('uid'=>$order['uid'],'number'=>$_REQUEST['trade_no']))->getField('id')){
				return true;		
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

            $money=M('Money');
                 
            //先将金钱充值到用户账户表中
	    	if($money->where(array('uid'=>$order['uid']))->setInc('money',$order['money'])){
                    
	    		//修改充值表的状态
	    		$db->where($where)->setField(array('time'=>$_SERVER['REQUEST'],'isbuy'=>1));
             
                 //生成充值记录
	    		if(M('Bill')->add($add)) {
	    			return true;
	    		}	

	    	}

	    	return false;
      } 

}
