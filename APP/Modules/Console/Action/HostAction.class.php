<?php
/*
	@der 主机租用/托管控制器
*/
Class HostAction extends CommonAction{

	/**
		@der 主机租用列表
	*/
	public function index(){
		$where = array('uid' => $_SESSION['id'] , 'gid' => C('rent_yun') , 'status' => array('NEQ',0) ,'isdelete' => 0,'host_rent.type' => 0);
		$this->data = $this->get_result($where);
		$this->display();
	}

	/**
		@der 主机托管列表
	*/
	public function trusteeship(){
		$where = array('uid' => $_SESSION['id'] , 'gid' => C('trusteeship_yun') , 'status' => array('NEQ',0) ,'isdelete' => 0,'host_rent.type' => 1);
	
		$this->data = $this->get_result($where);
		$this->display('index');
	}

	/**
		@der 获取结果集
	*/
	private function get_result($where){
		$db    = D('Host_rentView');		
		$data  = $db->where($where)->select();
		
		return $data;
	}
}