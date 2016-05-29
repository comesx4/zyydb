<?php
/*
	@der 命令日志管理
*/
Class LogsCommandAction extends CommonAction{

	/*
		@der 列表页
	*/
	public function index(){

		$db = D('Logs_commandView');
		list ($data,$tmp,$page) = $db->search();
	
		$this->data  = $data;
		$this->fpage = $page['1'];
		$this->tmp   = $tmp;
		$this->display();
	}

	/*
		@der 详情页面
	*/
	public function detail(){
		$where = array('id'=>I('get.id',1,'intval'));
		$data  = D('Logs_commandView')->where($where)->find();
		
		$this->data = $data;
		$this->display();
	}
}
