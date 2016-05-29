<?php
/*
	@der 云服务器控制器
*/
Class ServerAction extends CommonAction{

	/*
		@der 云服务器首页
	*/
	public function index(){
		
		/*查找出用户所有实例*/
		$db = D('LivingView');
		list($data,$tmp,$page) = $db->search();
		//操作系统
		$public_os = D('MirroringView')->where(array('type' => 0))->select();
		//公共镜像
		//自定义镜像
		$field = 'id,name';
		$custom	   = M('Mirroring')->field($field)->where(array('uid' => $_SESSION['id'], 'status' => 1) )->select();
		
		$this->fpage 	 = $page['1'];
		$this->custom    = $custom;
		$this->public_os = $public_os;
		$this->dbName 	 = $dbName;
		$this->living 	 = $data;
		$this->tmp   	 = $tmp;
		$this->display();
	}

	/*
		@der 实例详细页面			
	*/
	public function living(){

		$tid = I('get.tid',1,'intval');
		//$id  = I('get.id','','intval');
		$db  = M('Living');
		/*运行中的产品*/
		$where  = array('tid'=>$tid , 'living.uid' => $_SESSION['id']);
		$living = D('Yunji_serverView')->where($where)->find();		
	
		/*验证*/
		$living || redirect(U('index'));
		/*获取最后一条监控数据*/
		$new_monitor = D('Server')->getLastMonitor($living);
			
		/*获取监控指标列表*/
		$this->living  = $living;
		$this->time    = $time    = time()+(60);
		$this->monitor = json_decode(D('Server')->get_chart_data($tid,$living['name']),true);
		$this->new_monitor = $new_monitor;
		$this->display();
	}

	/*
		@der 返回图表所需要的数据
	*/
	public function chart_data(){
		if (!$this->isajax()) redirect(__APP__);
		
		echo D('Server')->get_chart_data();
	}

	/*
		@der 处理实例的操作
	*/
	public function livingOperation(){		
		if (!$this->ispost()) redirect(__APP__);

		/*处理重启、关机等一系列操作*/
		echo D('Server')->serverOperation(I('post.type',3,'intval'));
		
	}

	/*
		@der 处理计时器的请求
	*/
	public function responseTimer(){
		if ( !$this->isajax()) redirect(__APP__);	
		/*改变实例状态*/
		echo D('Server')->changeStatus(I('post.status','2','intval'));	
	}

	/*
		@der 重置密码
	*/
	public function resetPassword(){
		IS_POST || redirect(__APP__);
		$_POST['id'] = I('post.server_id');

		if (I('post.type')) {
			$code = decode($_COOKIE['checkEmail']);
		} else {
			$code = decode($_COOKIE['kz_'.$_SESSION['userinfo']['telephone']]);
		}

		if ( I('post.code') != $code || empty(I('post.code'))) {
			$this->error('验证码有误！'); die;
		}
		
		$result = json_decode(D('Server')->changePassword() , true);

		if ($result['status'] == 1) {
			$this->success('重置成功!',$_SERVER['HTTP_REFERER']);
		} else {
			$this->error($result['message']);
		}
	}

	/*
		@der 发送短信验证码
   */
	public function sendInformation(){
		if (!$this->isajax()) redirect(__APP__);

		/*导入短信发送类*/
		import('Class.Telephone',APP_PATH);		
		if ( !$message = Telephone::sendInformation($_SESSION['userinfo']['telephone']) ){
			echo json_encode(array('status'=>1));
		} else {
			echo json_encode(array('status'=>0,'message'=>$message));
		}
	}

	/*
		@der 验证短信验证码
	*/
	public function checkVerify(){
		if (!$this->isajax()) redirect(__APP__);
		
		if ( I('post.verify') == decode($_COOKIE['kz_'.$_SESSION['userinfo']['telephone']]) && I('post.verify') != '') {
			echo 'true';
		} else {
			echo 'false';
		}
	}

	/*
		@der 发送邮箱验证码
	*/
	public function sendEmail(){
		IS_AJAX || redirect(__APP__);
		$code = randStr(4);
		$content = "您在进行重置服务器密码操作，本次验证码为：{$code}(有效时间60秒)";
		send_email($_SESSION['uname'],'御智云充值密码',$content);

		setcookie("checkEmail",encode($code),$_SERVER['REQUEST_TIME']+190,'/');
		echo json_encode(array('status'=>1));
	}

	/*
		@der 验证邮箱验证码
	*/
	public function checkEmail(){
		if (!$this->isajax()) redirect(__APP__);

		if ( I('post.verify') == decode($_COOKIE['checkEmail']) ) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	/*
		@der 服务器改名
	*/
	public function reName(){
		IS_POST|| redirect(__APP__);
		$server_id = I('post.server_id');
		if (empty($server_id)) {
			$this->error('未选择实例');
		}

		$where    = array('id' => array('IN',implode(',' ,$server_id)) );
		$setField = array('server_alias' => I('post.newName',''));
		!$setField['server_alias'] && $this->error('名称不能为空');

		if (M('Yunji_server')->where($where)->setField($setField)) {
			$this->success('修改成功',U('index'));
		} else {
			$this->error('修改失败');
		}
	}

	/*
		@der 重装系统
	*/
	public function reInstallOs(){
		IS_POST|| redirect(__APP__);
		$_POST['id'] = I('post.server_id',0,'intval');

		$result = json_decode(D('Server')->reOs() , true);

		if ($result['status'] == 1) {
			$this->success('重置成功!',$_SERVER['HTTP_REFERER']);
		} else {
			$this->error($result['message']);
		}
	}
}