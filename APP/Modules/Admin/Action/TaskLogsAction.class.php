<?php
/**
	@der 任务日志
*/
Class TaskLogsAction extends CommonAction{

	/**
		@der 列表页
	*/
	public function index(){
		$db = D('Task_logsView');
		list ($data,$tmp,$page) = $db->search();
		
		$this->data  = $data;
		$this->fpage = $page['1'];
		$this->tmp   = $tmp;
		$this->goods = show_list('goods','goods_id','goods',$tmp['goods_id']);
		$this->display();
	}
}