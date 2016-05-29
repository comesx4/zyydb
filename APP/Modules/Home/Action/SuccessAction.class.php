 <?php
Class SuccessAction extends Action{

	 /**
	  *		购买产品成功后支付宝异步请求的页面
	  *8.2  服务器异步通知页面特性 
		(1) 必须保证服务器异步通知页面（notify_url）上无任何字符，如空格、HTML 标签、开发系统自带抛出的异常提示信息等； (2) 支付宝是用 POST 方式发送通知信息，因此该页面中获取参数的方式，如： request.Form("out_trade_no")、$_POST['out_trade_no']； (3) 支付宝主动发起通知，该方式才会被启用； (4) 只有在支付宝的交易管理中存在该笔交易，且发生了交易状态的改变，支付 宝才会通过该方式发起服务器通知（即时到账中交易状态为“等待买家付款” 的状态默认是不会发送通知的）； (5) 服务器间的交互，不像页面跳转同步通知可以在页面上显示出来，这种交互 方式是不可见的； (6) 第一次交易状态改变（即时到账中此时交易状态是交易完成）时，不仅页面 跳转同步通知页面会启用，而且服务器异步通知页面也会收到支付宝发来的 处理结果通知； (7) 程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支 付宝的字符不是 success 这 7 个字符，支付宝服务器会不断重发通知，直到 超过 24 小时 22 分钟。 一般情况下，25 小时以内完成 8 次通知（通知的间隔频率一般是： 2m,10m,10m,1h,2h,6h,15h）； (8) 程序执行完成后，该页面不能执行页面跳转。如果执行页面跳转，支付宝会 收不到 success 字符，会被支付宝服务器判定为该页面程序运行出现异常， 而重发处理结果通知
	  */
	public function index(){
		
		import('Class.alipay_notify',APP_PATH);
        //从配置文件中读取支付宝配置信息
        $config=F('zhifubao','','./APP/Conf/');	  
		
		unset($_GET['_URL_']);
	 
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($config);
	    $verify_result = $alipayNotify->verifyNotify();
 
		if($verify_result) {//验证成功
       
			//导入支付宝验证类
			import('Class.Zhifubao',APP_PATH);
			$zhifubao = new Zhifubao();
			$status=$zhifubao->returncent();

			if($status){				
				die('success');
			}else{				
				die('error');
			}

		    if($_REQUEST['trade_status'] == 'TRADE_FINISHED') {

		    }
		    else if ($_REQUEST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
						
				//注意：
				//付款完成后，支付宝系统发送该交易状态通知

		        //调试用，写文本函数记录程序运行情况是否正常
		        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		    }
		}
		else {
		    //验证失败
		    echo "fail";

		    //调试用，写文本函数记录程序运行情况是否正常
		    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}

	}

    //购买产品成功后用户点击返回的页面
	public function success(){
		
		import('Class.alipay_notify',APP_PATH);
	
        //从配置文件中读取支付宝配置信息
        $config=F('zhifubao','','./APP/Conf/');	  
		
		unset($_GET['_URL_']);

	 
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($config);
	
		$verify_result = $alipayNotify->verifyReturn();
	
 
		if($verify_result) {//验证成功
       
			//导入支付宝验证类
			import('Class.Zhifubao',APP_PATH);

			$zhifubao=new Zhifubao();

			$status = $zhifubao->returncent();

			// if($status){
			// 	echo '充值成功';
			// }else{
			// 	echo '充值失败';
			// }

			$this->display();

		    if($_REQUEST['trade_status'] == 'TRADE_FINISHED') {
		   

		    }
		    else if ($_REQUEST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
						
				//注意：
				//付款完成后，支付宝系统发送该交易状态通知

		        //调试用，写文本函数记录程序运行情况是否正常
		        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		    }

		
		        
			//请不要修改或删除
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
		    //验证失败
		    echo "fail";

		    //调试用，写文本函数记录程序运行情况是否正常
		    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}

	}
}