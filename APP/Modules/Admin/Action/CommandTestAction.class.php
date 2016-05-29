<?php
/**
	@der 命令测试
*/
Class CommandTestAction extends CommonAction{

	/**
		@der 命令测试页面
	*/
	public function index(){
		$field = array('id','name','title','rule','remark','status','time');
		$data  = D('CommandRelation')->relation('tag')->field($field)->order('id ASC')->select();
		
		$this->data = $data;
		$this->display();
	}

	/**
		@der 发送命令
	*/
	public function sendCommand(){
		IS_POST || redirect(__APP__);
		//验证母服务器IP
		$where = array('server_ip' => I('post.server_ip'));
		$send = M('Mu_server')->field('server_ip,secret_key,server_port')->where($where)->find();
		
		if (!$send) {
			die(0);
		}

		$send_str = '<?xml version="1.0" encoding="utf-8"?>'.$_POST['xml'];
		import('Class.Server',APP_PATH);
		
		$result = Server::sendCommand($send_str, $send['server_ip'] , $send['server_port'] , $send['secret_key']);

		p($result);
	}
}